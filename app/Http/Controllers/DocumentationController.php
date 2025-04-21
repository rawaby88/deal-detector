<?php

namespace App\Http\Controllers;


use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Support\Facades\File;

class DocumentationController extends Controller
{
    public function __invoke()
    {
        $markdown = File::get(base_path('README.md'));

        $content = Markdown::convertToHtml($markdown);
        $content = $this->processHtmlHeadings($content);

        return view('documentation.index', compact('content'));
    }

    private function processHtmlHeadings($html)
    {
        $pattern = '/<h([1-6])>([^<]+)<\/h\1>/';
        return preg_replace_callback($pattern, function($matches) {
            $level = $matches[1];
            $text = $matches[2];
            $id = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $text), '-'));
            return "<h{$level} id=\"{$id}\">{$text}</h{$level}>";
        }, $html);
    }
}
