<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\ContentVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VersionControlController extends Controller
{
    public function index()
    {
        return view('admin.version_control.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $content = Content::create([
            'title'              => $request->title,
            'content'            => $request->content,
            'status'             => $request->status,
            'current_version_id' => null,
        ]);

        $version = $this->createVersionSnapshot($content, 'no');

        $content->update([
            'current_version_id' => (string) $version->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Content saved successfully.',
            'data'    => $content->fresh(),
        ]);
    }

    public function list()
    {
        $contents = Content::latest()->get();
        return view('admin.version_control.list', compact('contents'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'required|in:draft,published',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $content = Content::findOrFail($id);

        $content->update([
            'title'   => $request->title,
            'content' => $request->content,
            'status'  => $request->status,
        ]);

        $version = $this->createVersionSnapshot($content, 'no');

        $content->update([
            'current_version_id' => (string) $version->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Content updated successfully.',
            'data'    => $content->fresh(),
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

    public function history($id)
    {
        $content = Content::findOrFail($id);
        $versions = ContentVersion::where('content_id', (string) $content->id)
            ->latest()
            ->get();

        return response()->json([
            'success'  => true,
            'content'  => $content,
            'versions' => $versions,
        ]);
    }

    public function restore($contentId, $versionId)
    {
        $content = Content::findOrFail($contentId);
        $version = ContentVersion::where('content_id', (string) $content->id)->findOrFail($versionId);

        $this->createVersionSnapshot($content, 'no');

        $content->update([
            'title'              => $version->title,
            'content'            => $version->content,
            'current_version_id' => (string) $version->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Version restored successfully.',
            'data'    => $content->fresh(),
        ]);
    }

    private function createVersionSnapshot(Content $content, string $isAutoSave = 'no'): ContentVersion
    {
        $nextVersionNumber = (string) (
            ContentVersion::where('content_id', (string) $content->id)->count() + 1
        );

        return ContentVersion::create([
            'content_id'      => (string) $content->id,
            'title'           => $content->title,
            'content'         => $content->content,
            'version_number'  => $nextVersionNumber,
            'is_auto_save'    => $isAutoSave,
        ]);
    }
}
