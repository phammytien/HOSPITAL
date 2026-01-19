<?php

namespace App\Services;

use Illuminate\Support\Str;

class DocumentParserService
{
    /**
     * Extract notification data from uploaded document
     */
    public function extractNotificationData($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if ($extension === 'pdf') {
            return $this->parsePdfDocument($file);
        } elseif (in_array($extension, ['doc', 'docx'])) {
            return $this->parseWordDocument($file);
        }
        
        throw new \Exception('Unsupported file type');
    }
    
    /**
     * Parse Word document (.docx)
     * Simple approach: extract text from XML
     */
    private function parseWordDocument($file)
    {
        try {
            $content = '';
            
            // .docx is a ZIP file containing XML
            $zip = new \ZipArchive();
            if ($zip->open($file->getRealPath()) === true) {
                // Extract text from word/document.xml
                $xml = $zip->getFromName('word/document.xml');
                if ($xml) {
                    // Remove XML tags to get plain text
                    $xml = simplexml_load_string($xml);
                    if ($xml) {
                        foreach ($xml->xpath('//w:t') as $text) {
                            $content .= (string)$text . ' ';
                        }
                    }
                }
                $zip->close();
            }
            
            return $this->extractTitleAndContent($content);
            
        } catch (\Exception $e) {
            throw new \Exception('Cannot parse Word document: ' . $e->getMessage());
        }
    }
    
    /**
     * Parse PDF document
     * Simple approach: extract text using php functions
     */
    private function parsePdfDocument($file)
    {
        try {
            $content = '';
            $path = $file->getRealPath();
            
            // Try to extract text from PDF
            // This is a simple approach, may not work for all PDFs
            $fileContent = file_get_contents($path);
            
            // Extract text between stream markers
            if (preg_match_all("/\(([^)]+)\)/", $fileContent, $matches)) {
                $content = implode(' ', $matches[1]);
            }
            
            // Alternative: try to get readable text
            if (empty($content)) {
                $content = $this->extractTextFromPdf($fileContent);
            }
            
            return $this->extractTitleAndContent($content);
            
        } catch (\Exception $e) {
            throw new \Exception('Cannot parse PDF document: ' . $e->getMessage());
        }
    }
    
    /**
     * Extract readable text from PDF content
     */
    private function extractTextFromPdf($content)
    {
        // Remove binary data and keep only readable characters
        $text = preg_replace('/[^\x20-\x7E\x0A\x0D]/u', '', $content);
        
        // Clean up multiple spaces
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }
    
    /**
     * Extract title and content from text
     * Logic: Use first line or first 10 words as title
     */
    private function extractTitleAndContent($text)
    {
        $text = trim($text);
        
        if (empty($text)) {
            return [
                'title' => 'Thông báo',
                'message' => '',
                'type' => 'info'
            ];
        }
        
        // Split into lines
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $lines = array_filter($lines, function($line) {
            return !empty(trim($line));
        });
        $lines = array_values($lines);
        
        $title = '';
        $message = '';
        
        if (count($lines) > 0) {
            // Use first line as title
            $firstLine = trim($lines[0]);
            
            // If first line is too long, take first 10 words
            $words = explode(' ', $firstLine);
            if (count($words) > 10) {
                $title = implode(' ', array_slice($words, 0, 10)) . '...';
            } else {
                $title = $firstLine;
            }
            
            // Remaining lines as content
            if (count($lines) > 1) {
                $message = implode("\n", array_slice($lines, 1));
            } else {
                // If only one line, split: first 10 words = title, rest = content
                if (count($words) > 10) {
                    $message = implode(' ', array_slice($words, 10));
                }
            }
        }
        
        // Fallback
        if (empty($title)) {
            $title = 'Thông báo';
        }
        
        if (empty($message)) {
            $message = $text;
        }
        
        return [
            'title' => Str::limit($title, 200),
            'message' => $message,
            'type' => 'info'
        ];
    }
}
