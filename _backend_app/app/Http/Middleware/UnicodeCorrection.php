<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnicodeCorrection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $data = $request->all();

        $data = $this->replaceUnicodeRecursive($data);

        $request->replace($data);

        return $next($request);
    }
    private function replaceUnicodeRecursive($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->replaceUnicodeRecursive($value);
            }
        } else {
            $data = (is_string($data) && $this->isPlainTextString($data)) ? $this->replaceUnicode(preg_replace('/[ \t\x0B\f\x{A0}\x{2000}-\x{200B}\x{202F}\x{205F}\x{3000}]+/u', ' ', $data)) : $data;
        }
        return $data;
    }
    private function replaceUnicode($text)
    {
        $salabpi = ['ង', 'ញ', 'ប', 'ម', 'យ', 'រ', 'វ'];
        $treysab = ['ស', 'ហ', 'អ'];
        $chars = array_merge($salabpi, $treysab);
        $vowels = ['ិ', 'ី', 'ឹ', 'ឺ', 'ើ'];

        // Direct replacements
        $text = str_replace('្' . 'ដ', '្ត', $text);
        $text = str_replace('ា' . 'ំ', 'ាំ', $text);
        $text = str_replace('េ' . 'ី', 'ើ', $text);
        $text = str_replace('េ' . 'ា', 'ោ', $text);
        $text = str_replace('េ' . 'ះ', 'េះ', $text);
        $text = str_replace('ោ' . 'ះ', 'ោះ', $text);
        $text = str_replace('េ' . 'ុ' . 'ី', 'ុ' . 'ើ', $text);

        foreach ($chars as $char) {
            foreach ($vowels as $vowel) {
                if (in_array($char, $salabpi)) {
                    $replacementSign = '៉';
                } elseif (in_array($char, $treysab)) {
                    $replacementSign = '៊';
                } else {
                    continue;
                }
                $word = $char . 'ុ' . $vowel;
                $replacement = $char . $replacementSign . $vowel;
                $text = str_replace($word, $replacement, $text);
            }
        }
        return $text;
    }
    private function isPlainTextString($data)
    {
        if (!is_string($data)) {
            return false;
        }

        // Skip empty or very short strings
        if (strlen(trim($data)) < 3) {
            return true;
        }

        // Check for data URI schemes (data:image/*, data:application/*, etc.)
        if (preg_match('/^data:[a-zA-Z0-9\/\-\+]+;base64,/', $data)) {
            return false;
        }

        // Check for base64 image patterns
        if (preg_match('/^[A-Za-z0-9+\/]+=*$/', $data) && strlen($data) > 100) {
            // Additional check: try to detect if it's actually base64 encoded image
            $decoded = base64_decode($data, true);
            if ($decoded !== false) {
                // Check for common image file signatures
                $imageSignatures = [
                    "\xFF\xD8\xFF",     // JPEG
                    "\x89PNG\r\n\x1A\n", // PNG
                    "GIF87a",           // GIF87a
                    "GIF89a",           // GIF89a
                    "RIFF",             // WebP (starts with RIFF)
                    "\x00\x00\x01\x00", // ICO
                    "BM",               // BMP
                ];

                foreach ($imageSignatures as $signature) {
                    if (strpos($decoded, $signature) === 0) {
                        return false; // It's an image
                    }
                }
            }
        }

        // Check for file extensions in the string
        if (preg_match('/\.(jpg|jpeg|png|gif|webp|bmp|ico|pdf|doc|docx|xls|xlsx|zip|rar)$/i', $data)) {
            return false;
        }

        // Check for binary data indicators
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $data)) {
            return false;
        }

        // Check for common file format headers in the string
        $binaryHeaders = ['%PDF', 'PK\x03\x04', '\x50\x4B', 'MZ', '\x7F\x45\x4C\x46'];
        foreach ($binaryHeaders as $header) {
            if (strpos($data, $header) === 0) {
                return false;
            }
        }

        return true; // It's likely plain text
    }
}
