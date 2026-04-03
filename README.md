# AI CMS

AI CMS is a Laravel-based admin content platform that combines AI-assisted content workflows with practical editorial
tools. The project currently includes AI content generation, AI-powered editing, AI translation, version control, draft
management, analytics tracking, AI-powered content insights, and export/sharing tools, all wrapped in an admin dashboard
UI with asynchronous table actions.

The platform also includes role-based access control for `admin`, `editor`, and `viewer` users so navigation, route
access, and table actions match each user's responsibilities.

<img src="/public/docs/p1.png">

## Overview

This project is designed to help content teams:

- generate content with AI
- edit and refine saved content
- translate content into multiple languages
- manage draft and published states
- store version history on every meaningful update
- restore older versions when needed
- track content engagement performance
- generate AI insights from analytics signals
- export content as PDF or Word
- share published content through public links and social share URLs

## Current Features

### 1. AI Content Generation

<img src="/public/docs/p2.png">

- Generate content from a prompt and selected content type
- Save generated content to the database
- View saved generated content in a DataTable
- Async `view`, `edit`, and `delete` actions

### 2. AI Content Editing

- Improve content using AI edit modes:
    - grammar
    - professional tone
    - SEO optimization
    - rewrite
- Save each edited result into `content_edits`
- View saved edit records in a list table
- Async `view`, `edit`, and `delete` actions

### 3. AI Translation

- Translate content into:
    - Bangla
    - English
    - Hindi
    - Arabic
    - Spanish
- Save translated records into `translations`
- Translation flow is tuned to return only the selected target language output
- Translation history includes async `view`, `edit`, and `delete`

### 4. Version Control and Drafts

- Create content as `draft` or `published`
- Save a new entry in `content_versions` whenever content is created or updated
- Track `current_version_id` in `contents`
- View version history for each content item
- Restore older versions while preserving the current content as a new snapshot first

### 5. Dashboard UX Improvements

- DataTables-based list screens
- SweetAlert-powered async modal operations
- Inline modal editing for multiple modules
- Sidebar navigation for all major AI and content workflows
- Role-aware dashboard shortcuts and quick-access cards

### 6. Analytics & Insights

- Track content engagement through:
    - views
    - likes
    - shares
- Aggregate content performance into dashboard metrics
- Show top-performing and lowest-performing content
- Visualize data using Chart.js
- Generate AI-powered content recommendations based on analytics
- Save insights into `content_insights`
- Manage insights with async `view`, `edit`, and `delete`

### 7. Export & Sharing

- Export content as:
    - PDF
    - Word (`.docx`)
- Generate a clean printable export layout
- Create public content URLs for published content
- Share published content with:
    - copy link
    - Facebook
    - Twitter (X)
    - LinkedIn
- Manage exportable content with async `view`, `edit`, and `delete`

## Main Modules

The application currently includes these admin areas:

- Users
- Content Generation
- Content List
- Content Edit
- Content Edit List
- Content Translation
- Translation List
- Version Control
- Version List
- Analytics Dashboard
- Insights List
- Export & Sharing

## Role-Based Access

The application currently uses three roles:

- `admin`: full access to all modules, creation pages, AI actions, delete actions, exports, sharing, analytics
  dashboard, and user management
- `editor`: access to dashboard, list pages, version history, and record editing/restoring where supported
- `viewer`: access to dashboard and read-only list pages only

Current rules include:

- only `admin` can access the `Users` section
- only `admin` can access create/generate/process pages such as content generation, content editing, translation
  processing, analytics generation, and exports
- `editor` can work with list/history-oriented flows and update records where allowed
- `viewer` can open tables and inspect records, but does not get edit/delete controls

## Tech Stack

- PHP
- Laravel
- Blade
- jQuery
- DataTables
- SweetAlert2
- Feather Icons
- Chart.js
- DOMPDF
- PHPWord
- Hugging Face Inference API

## AI Integration

The AI features use the Hugging Face router endpoint and an API key stored in environment variables.

Required environment variable:

```env
HF_API_KEY=your_huggingface_api_key
```

The current AI workflows include:

- content generation
- content editing
- content translation
- analytics insight generation

## Database Tables In Use

The application currently relies on these main tables for the content workflows:

- `users`
- `ai_contents` or existing content-generation storage table used by the project
- `content_edits`
- `translations`
- `contents`
- `content_versions`
- `content_analytics`
- `content_insights`

## Project Structure Highlights

Important controllers currently involved:

- `AIContentController`
- `AIContentEditController`
- `AITranslationController`
- `VersionControlController`
- `AnalyticsController`
- `ExportSharingController`
- `Admin\UserController`

Important views currently involved:

- `resources/views/admin/ai_content.blade.php`
- `resources/views/admin/ai_contents_list.blade.php`
- `resources/views/admin/ai/editor.blade.php`
- `resources/views/admin/ai/translation.blade.php`
- `resources/views/admin/ai_edit_contents_list.blade.php`
- `resources/views/admin/translation_list.blade.php`
- `resources/views/admin/version_control/index.blade.php`
- `resources/views/admin/version_control/list.blade.php`
- `resources/views/admin/analytics/index.blade.php`
- `resources/views/admin/analytics/insights_list.blade.php`
- `resources/views/admin/export_sharing/index.blade.php`
- `resources/views/exports/content.blade.php`
- `resources/views/public/content.blade.php`
- `resources/views/components/sidebar/sidebar.blade.php`

## Installation

### 1. Clone the repository

```bash
git clone <your-repository-url>
cd ai-cms
```

### 2. Install dependencies

```bash
composer install
```

If frontend dependencies are used in your environment:

```bash
npm install
```

### 3. Create environment file

```bash
cp .env.example .env
```

On Windows PowerShell, if needed:

```powershell
Copy-Item .env.example .env
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Configure database and API credentials

Update `.env` with:

- database connection details
- `HF_API_KEY`
- `APP_URL`

Example:

```env
APP_URL=https://your-public-domain.com
```

### 6. Run migrations

```bash
php artisan migrate
```

### 7. Start the application

```bash
php artisan serve
```

If frontend assets are needed:

```bash
npm run dev
```

## Workflow Summary

### Content Generation Flow

1. Admin opens the generation page
2. AI generates content from prompt and type
3. Result can be saved
4. Saved content appears in the content list
5. Editors and viewers can access the saved list based on their role permissions

### Content Edit Flow

1. Admin opens the editor
2. Chooses an edit mode
3. AI improves the content
4. Result is stored in `content_edits`
5. Admin and editors can manage records from the edit list
6. Viewers can inspect records from the list in read-only mode

### Translation Flow

1. Admin enters content to translate
2. Selects target language
3. AI returns translated output
4. Result is stored in `translations`
5. Admin and editors can manage records from the translation list
6. Viewers can inspect the translation list in read-only mode

### Version Control Flow

1. Admin creates content as draft or published
2. A version snapshot is created automatically
3. Every update creates another version snapshot
4. History modal shows previous versions
5. Restore replaces current content with a selected version and preserves the previous current state
6. Editors can review history, edit content, and restore versions
7. Viewers can open the version list and inspect history without modification actions

### Analytics Flow

1. Admin opens the analytics dashboard
2. Engagement events are tracked as views, likes, or shares
3. Aggregate metrics are shown instead of raw event rows
4. Charts visualize overall performance
5. AI insights can be generated for a content item and stored in `content_insights`
6. Editors can manage the stored insights list
7. Viewers can read the insights list without edit/delete controls

### Export & Sharing Flow

1. Admin opens the export and sharing page
2. Selects a content item from the table
3. Exports it as PDF or Word when needed
4. Opens the share modal for a published content item
5. Copies the public link or shares it through Facebook, Twitter (X), or LinkedIn
6. Public readers access the content through `/content/{slug}`

### Engagement Rate Formula

The current engagement rate is calculated as:

```text
((likes + shares) / views) * 100
```

Or written directly:

```text
engagement rate = ((likes + shares) / views) * 100
```

Notes:

- if `views = 0`, engagement rate is `0`
- the value is rounded to 2 decimal places
- likes and shares are currently treated as equal engagement actions

## Roadmap Ideas

Possible next improvements:

- version comparison / diff viewer
- auto-save drafts every few seconds
- user-based ownership on translations and version-controlled content
- publish workflow approvals
- public-facing rendering for published content
- search and filtering across content records
- raw `content_events` table for event-level tracking
- weighted engagement scoring
- tracked social-share metrics connected to analytics

## Notes

- Translation currently enforces the selected target language output as much as possible through prompt design and
  response cleanup.
- Version restore currently reloads the page after a successful restore to guarantee the table reflects the new current
  state.
- Some list screens use async redraw behavior so the user does not need a full refresh for normal edit/delete
  operations.
- Analytics currently stores aggregate values in `content_analytics` rather than a separate raw `content_events` table.
- Social platforms such as Facebook and LinkedIn require a real public URL. `localhost` links will not work for external
  sharing.
- Public sharing is currently limited to content with `published` status.
- Sidebar visibility, route access, and DataTable action buttons are role-aware and currently aligned with the `admin`,
  `editor`, and `viewer` permissions described above.

## License

This project is open for internal/project-specific use unless you define a separate license for distribution.
