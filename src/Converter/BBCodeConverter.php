<?php

/**
 * @file BBCodeConverter.php
 * @brief This file contains the BBCodeConverter class.
 * @details
 * @author Filippo F. Fadda
 */

namespace Converter;


/**
 * @brief A rudimental converter that takes as input a BBCode formatted text and converts it to Markdown.
 */
class BBCodeConverter extends Converter
{
    
    
    /**
     * @brief Removes BBCode size.
     */
    protected function removeSize()
    {
        
        $this->text = preg_replace_callback('%\[size=\d*\]([\W\D\w\s]*?)\[/size\]%iu',
            
            function ($matches) {
                return $matches[1];
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Removes BBCode center.
     */
    protected function removeCenter()
    {
        
        $this->text = preg_replace_callback('%\[center\]([\W\D\w\s]*?)\[/center\]%iu',
            
            function ($matches) {
                return $matches[1];
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Removes BBCode center.
     */
    protected function removeLeft()
    {
        
        $this->text = preg_replace_callback('%\[left\]([\W\D\w\s]*?)\[/left\]%iu',
            
            function ($matches) {
                return $matches[1];
            },
            
            $this->text
        );
        
    }
    
    /**
     * @brief Removes BBCode color.
     */
    protected function removeColor()
    {
        
        $this->text = preg_replace_callback('%\[color[=?][#?][\w]*\]([\W\D\w\s]*?)\[/color\]%iu',
            
            function ($matches) {
                return $matches[1];
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Replaces BBCode bold.
     */
    protected function replaceBold()
    {
        
        $this->text = preg_replace_callback('%\[b\]([\W\D\w\s]*?)\[/b\]%iu',
            
            function ($matches) {
                return "**" . trim($matches[1], " ") . "**";
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Replaces BBCode italic.
     */
    protected function replaceItalic()
    {
        
        $this->text = preg_replace_callback('%\[i\]([\W\D\w\s]*?)\[/i\]%iu',
            
            function ($matches) {
                return "*" . trim($matches[1], " ") . "*";
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Replaces BBCode underline. Hoedown support underline.
     */
    protected function replaceUnderline()
    {
        
        $this->text = preg_replace_callback('%\[u\]([\W\D\w\s]*?)\[/u\]%iu',
            
            function ($matches) {
                return "_" . trim($matches[1], " ") . "_";
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Replaces BBCode strikethrough.
     */
    protected function replaceStrikethrough()
    {
        
        $this->text = preg_replace_callback('%\[s\]([\W\D\w\s]*?)\[/s\]%iu',
            
            function ($matches) {
                return "~~" . trim($matches[1], " ") . "~~";
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Replaces BBCode lists.
     */
    protected function replaceLists()
    {
        
        $this->text = preg_replace_callback('%\[list(?P<type>=1)?\](?P<items>[\W\D\w\s]*?)\[/list\]%iu',
            
            function ($matches) {
                $buffer = "";
                
                $list = preg_replace('/\s*$|^\s*/mu', '', $matches['items']);
                if ( is_null($list) )
                    throw new \RuntimeException(sprintf("Text identified by '%d' has malformed BBCode lists", $this->id));
                
                $items = preg_split('/\[\*\]/u', $list);
                
                $counter = count($items);
                
                if ( isset($matches['type']) && $matches['type'] == '=1' ) { // ordered list
                    // We start from 1 to discard the first string, in fact, it's empty.
                    for ( $i = 1; $i < $counter; $i++ )
                        if ( !empty($items[ $i ]) )
                            $buffer .= (string) ( $i ) . '. ' . trim($items[ $i ]) . PHP_EOL;
                } else { // unordered list
                    // We start from 1 to discard the first string, in fact, it's empty.
                    for ( $i = 1; $i < $counter; $i++ )
                        if ( !empty($items[ $i ]) )
                            $buffer .= '- ' . trim($items[ $i ]) . PHP_EOL;
                }
                
                // We need a like break above the list and another one below.
                if ( !empty($buffer) )
                    $buffer = PHP_EOL . $buffer . PHP_EOL;
                
                return $buffer;
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Replaces BBCode urls.
     */
    protected function replaceUrls()
    {
        
        $this->text = preg_replace_callback('%\[url\s*=\s*("(?:[^"]*")|\A[^\']*\Z|(?:[^\'">\]\s]+))\s*(?:[^]\s]*)\]([\W\D\w\s]*?)\[/url\]%iu',
            
            function ($matches) {
                if ( isset($matches[1]) && isset($matches[2]) )
                    return "[" . $matches[2] . "](" . $matches[1] . ")";
                else
                    throw new \RuntimeException(sprintf("Text identified by '%d' has malformed BBCode urls", $this->id));
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Replaces BBCode urls without a description.
     */
    protected function replaceUndescribedUrls()
    {
        $this->text = preg_replace_callback('%\[url\]([\W\D\w\s]*?)\[/url\]%iu',
            
            function ($matches) {
                if ( isset($matches[1]) )
                    return "[" . $matches[1] . "](" . $matches[1] . ")";
                else
                    throw new \RuntimeException(sprintf("Text identified by '%d' has malformed BBCode urls", $this->id));
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Replaces BBCode images.
     */
    protected function replaceImages()
    {
        
        $this->text = preg_replace_callback('%\[img\s*\]\s*("(?:[^"]*")|\A[^\']*\Z|(?:[^\'">\]\s]+))\s*(?:[^]\s]*)\[/img\]%iu',
            
            function ($matches) {
                if ( isset($matches[1]) )
                    return PHP_EOL . "![]" . "(" . $matches[1] . ")" . PHP_EOL;
                else
                    throw new \RuntimeException(sprintf("Text identified by '%d' have malformed BBCode images", $this->id));
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Replaces BBCode quotes.
     * @details Thanks to Casimir et Hippolyte for helping me with this regex.
     */
    protected function replaceQuotes()
    {
        // Removes the inner quotes, leaving just one level.
        $this->text = preg_replace('~\G(?<!^)(?>(\[quote\b[^]]*](?>[^[]++|\[(?!/?quote)|(?1))*\[/quote])|(?<!\[)(?>[^[]++|\[(?!/?quote))+\K)|\[quote\b[^]]*]\K~', '', $this->text);
        
        // Replaces all the remaining quotes with '> ' characters.
        $this->text = preg_replace_callback('%\[quote\b[^]]*\]((?>[^[]++|\[(?!/?quote))*)\[/quote\]%i',
            
            function ($matches) {
                $quote = preg_replace('/^\s*/mu', '', trim($matches[1]));
                return "> " . $quote . PHP_EOL . PHP_EOL;
            },
            
            $this->text
        );
    }
    
    
    /**
     * @brief Replaces BBCode snippets.
     */
    protected function replaceSnippets()
    {
        
        $this->text = preg_replace_callback('%\[code\s*=?(?P<language>\w*)\](?P<snippet>[\W\D\w\s]*?)\[\/code\]%iu',
            
            function ($matches) {
                if ( isset($matches['snippet']) ) {
                    $language = strtolower($matches['language']);
                    
                    if ( $language == 'html4strict' or $language == 'div' )
                        $language = 'html';
                    elseif ( $language == 'shell' or $language == 'dos' or $language == 'batch' )
                        $language = 'sh';
                    elseif ( $language == 'xul' or $language == 'wpf' )
                        $language = 'xml';
                    elseif ( $language == 'asm' )
                        $language = 'nasm';
                    elseif ( $language == 'vb' or $language == 'visualbasic' or $language == 'vba' )
                        $language = 'vb.net';
                    elseif ( $language == 'asp' )
                        $language = 'aspx-vb';
                    elseif ( $language == 'xaml' )
                        $language = 'xml';
                    elseif ( $language == 'cplusplus' )
                        $language = 'cpp';
                    elseif ( $language == 'txt' or $language == 'gettext' )
                        $language = 'text';
                    elseif ( $language == 'basic' )
                        $language = 'cbmbas';
                    elseif ( $language == 'lisp' )
                        $language = 'clojure';
                    elseif ( $language == 'aspnet' )
                        $language = 'aspx-vb';
                    
                    return PHP_EOL . "```" . $language . PHP_EOL . trim($matches['snippet']) . PHP_EOL . "```" . PHP_EOL;
                } else
                    throw new \RuntimeException(sprintf("Text identified by '%d' has malformed BBCode snippet.", $this->id));
            },
            
            $this->text
        );
        
    }
    
    
    /**
     * @brief Converts the provided BBCode text to an equivalent Markdown text.
     */
    public function toMarkdown()
    {
        $this->removeCenter();
        $this->removeLeft();
        $this->removeColor();
        $this->removeSize();
        $this->replaceBold();
        $this->replaceItalic();
        $this->replaceUnderline();
        $this->replaceStrikethrough();
        $this->replaceLists();
        $this->replaceUrls();
        $this->replaceUndescribedUrls();
        $this->replaceImages();
        $this->replaceQuotes();
        $this->replaceSnippets();
        
        return trim($this->text);
    }
    
} 