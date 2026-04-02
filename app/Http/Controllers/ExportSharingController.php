<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentVersion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ExportSharingController extends Controller
{
    public function index()
    {
        $contents = Content::latest()->get();
        return view('admin.export_sharing.index', compact('contents'));
    }

    public function publicShow(string $slug)
    {
        $content = $this->findContentBySlug($slug);

        abort_if($content->status !== 'published', 404);

        return view('public.content', compact('content'));
    }

    public function exportPdf($id)
    {
        $content = Content::findOrFail($id);

        $pdf = Pdf::loadView('exports.content', [
            'content' => $content,
            'author' => auth()->user()?->name ?? 'Admin',
            'exportedAt' => now(),
        ])->setPaper('a4');

        return $pdf->download($this->makeFileName($content, 'pdf'));
    }

    public function exportWord($id)
    {
        $content = Content::findOrFail($id);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'marginTop' => 900,
            'marginBottom' => 900,
            'marginLeft' => 900,
            'marginRight' => 900,
        ]);

        $section->addText($content->title, ['bold' => true, 'size' => 18], ['spaceAfter' => 240]);
        $section->addText('Status: ' . ucfirst($content->status), ['size' => 11, 'color' => '666666']);
        $section->addText('Date: ' . $content->updated_at->format('d M Y, h:i A'), ['size' => 11, 'color' => '666666'], ['spaceAfter' => 240]);
        $section->addText($content->content, ['size' => 12], ['spaceAfter' => 180]);

        $tempFile = storage_path('app/' . uniqid('content-export-') . '.docx');
        IOFactory::createWriter($phpWord, 'Word2007')->save($tempFile);

        return response()->download($tempFile, $this->makeFileName($content, 'docx'))->deleteFileAfterSend(true);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $content = Content::findOrFail($id);
        $content->update([
            'title' => $request->title,
            'content' => $request->content,
            'status' => $request->status,
        ]);

        $version = $this->createVersionSnapshot($content, 'no');
        $content->update([
            'current_version_id' => (string) $version->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Content updated successfully.',
            'data' => $content->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $content = Content::findOrFail($id);
        ContentVersion::where('content_id', (string) $content->id)->delete();
        $content->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content deleted successfully.',
        ]);
    }

    private function createVersionSnapshot(Content $content, string $isAutoSave = 'no'): ContentVersion
    {
        $nextVersionNumber = (string) (
            ContentVersion::where('content_id', (string) $content->id)->count() + 1
        );

        return ContentVersion::create([
            'content_id' => (string) $content->id,
            'title' => $content->title,
            'content' => $content->content,
            'version_number' => $nextVersionNumber,
            'is_auto_save' => $isAutoSave,
        ]);
    }

    private function findContentBySlug(string $slug): Content
    {
        $parts = explode('-', $slug);
        $id = end($parts);

        abort_unless(is_numeric($id), 404);

        return Content::findOrFail($id);
    }

    private function makeFileName(Content $content, string $extension): string
    {
        $title = \Illuminate\Support\Str::slug($content->title ?: 'content');
        return $title . '-' . $content->id . '.' . $extension;
    }
}
