<?php

namespace App\Services;

use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

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
            $zip = new \ZipArchive;

            // .docx is a ZIP file containing XML
            if ($zip->open($file->getRealPath()) === true) {
                // Extract text from word/document.xml
                $xmlContent = $zip->getFromName('word/document.xml');
                if ($xmlContent) {
                    // Method 1: Use XML parsing with namespace
                    try {
                        $xml = new \SimpleXMLElement($xmlContent);
                        $xml->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

                        // Get all paragraphs
                        $paragraphs = $xml->xpath('//w:p');
                        if ($paragraphs) {
                            foreach ($paragraphs as $p) {
                                // Extract text from each paragraph
                                $p->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
                                $textNodes = $p->xpath('.//w:t');
                                if ($textNodes) {
                                    $pText = '';
                                    foreach ($textNodes as $text) {
                                        $pText .= (string) $text;
                                    }
                                    if (!empty(trim($pText))) {
                                        $content .= $pText . "\n";
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // Method 2: Fallback to regex if XML parsing fails
                        // Extract paragraphs using regex
                        if (preg_match_all('/<w:p[^>]*>(.*?)<\/w:p>/s', $xmlContent, $pMatches)) {
                            foreach ($pMatches[1] as $pXml) {
                                if (preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/', $pXml, $tMatches)) {
                                    $pText = implode('', $tMatches[1]);
                                    if (!empty(trim($pText))) {
                                        $content .= $pText . "\n";
                                    }
                                }
                            }
                        }
                    }

                    // Cleanup HTML/XML entities
                    $content = html_entity_decode($content);
                }
                $zip->close();
            }

            return $this->extractTitleAndContent($this->cleanUtf8($content));

        } catch (\Exception $e) {
            throw new \Exception('Cannot parse Word document: ' . $e->getMessage());
        }
    }

    private function parsePdfDocument($file)
    {
        try {
            $path = $file->getRealPath();
            $parser = new Parser();
            $pdf = $parser->parseFile($path);

            // Get all text from PDF
            $content = $pdf->getText();

            // Clean up the text
            $content = $this->cleanPdfText($content);

            return $this->extractTitleAndContent($this->cleanUtf8($content));

        } catch (\Exception $e) {
            // Fallback to basic method if parser fails
            return $this->parsePdfDocumentBasic($file);
        }
    }

    /**
     * Basic PDF parsing method as fallback
     */
    private function parsePdfDocumentBasic($file)
    {
        try {
            $content = '';
            $path = $file->getRealPath();
            $fileContent = file_get_contents($path);

            if (preg_match_all("/\(([^)]+)\)/", $fileContent, $matches)) {
                $content = implode(' ', $matches[1]);
            }

            if (empty($content)) {
                $content = $this->extractTextFromPdf($fileContent);
            }

            return $this->extractTitleAndContent($this->cleanUtf8($content));
        } catch (\Exception $e) {
            throw new \Exception('Cannot parse PDF document: ' . $e->getMessage());
        }
    }

    /**
     * Clean up text extracted from PDF library
     */
    private function cleanPdfText($text)
    {
        // Replace multiple horizontal spaces with a single space
        $text = preg_replace('/[ \t]+/', ' ', $text);
        // Replace more than 2 newlines with exactly 2
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim($text);
    }

    /**
     * Extract readable text from PDF content
     */
    private function extractTextFromPdf($content)
    {
        // Remove binary data and keep only readable characters
        // Do not use /u flag here because $content is binary and may contain invalid UTF-8 sequences
        $text = preg_replace('/[^\x20-\x7E\x0A\x0D]/', '', $content);

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

        // Split into lines/paragraphs
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $lines = array_filter($lines, function ($line) {
            return !empty(trim($line));
        });
        $lines = array_values($lines);

        $title = '';
        $message = '';

        if (count($lines) > 0) {
            // Use first line as title
            $firstLine = trim($lines[0]);

            // Take the whole first line as title (limit to 200)
            $title = Str::limit($firstLine, 200);

            // Everything else is message
            if (count($lines) > 1) {
                $messageArray = array_slice($lines, 1);
                $message = implode("\n\n", $messageArray);
            } else {
                // If only one line, and it's long, we still treat it as title
                // and leave message empty to avoid duplication
                $message = '';
            }
        }

        // Fallback for title
        if (empty($title)) {
            $title = 'Thông báo mới';
        }

        return [
            'title' => Str::limit($this->cleanUtf8($title), 200),
            'message' => $this->cleanUtf8($message),
            'type' => 'info'
        ];
    }

    /**
     * Ensure string is valid UTF-8 by stripping invalid bytes
     */
    private function cleanUtf8($string)
    {
        if (!is_string($string)) {
            return '';
        }

        // Use mb_convert_encoding to strip invalid UTF-8 characters
        return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
    }
}
