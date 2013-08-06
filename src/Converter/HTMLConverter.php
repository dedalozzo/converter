<?php

//! @file HTMLConverter.php
//! @brief This file contains the HTMLConverter class.
//! @details
//! @author Filippo F. Fadda


namespace Converter;


//! @brief A rudimental converter that takes as input HTML and replaces tags with related BBCodes.
//! @detail This converter doesn't touch the HTML inside pre or code tags.
class HTMLConverter extends Converter {
  protected $snippets = [];


  // Let's find all code snippets inside the body. The code can be inside <pre></pre>, <code></code>, or [code][/code]
  // if you are using BBCode markup language.
  protected function removeSnippets() {
    $pattern = '%(?P<openpre><pre>)(?P<contentpre>[\W\D\w\s]*?)(?P<closepre></pre>)|(?P<opencode><code>)(?P<contentcode>[\W\D\w\s]*?)(?P<closecode></code>)|(?P<openbbcode>\[code=?\w*\])(?P<contentbbcode>[\W\D\w\s]*?)(?P<closebbcode>\[/code\])%i';

    if (preg_match_all($pattern, $this->text, $this->snippets)) {

      $pattern = '%<pre>[\W\D\w\s]*?</pre>|<code>[\W\D\w\s]*?</code>|\[code=?\w*\][\W\D\w\s]*?\[/code\]%i';

      // Replaces the code snippet with a special marker to be able to inject the code in place.
      $this->text = preg_replace($pattern, '___SNIPPET___', $this->text);
    }
  }


  //! @brief Restores the snippets, converting the HTML tags to BBCode tags.
  protected function restoreSnippets() {
    $snippetsCount = count($this->snippets[0]);

    for ($i = 0; $i < $snippetsCount; $i++) {
      // We try to determine which tags the code is inside: <pre></pre>, <code></code>, [code][/code]
      if (!empty($this->snippets['openpre'][$i]))
        $snippet = "[code]".PHP_EOL.trim($this->snippets['contentpre'][$i]).PHP_EOL."[/code]";
      elseif (!empty($this->snippets['opencode'][$i]))
        $snippet = "[code]".PHP_EOL.trim($this->snippets['contentcode'][$i]).PHP_EOL."[/code]";
      else
        $snippet = $this->snippets['openbbcode'][$i].PHP_EOL.trim($this->snippets['contentbbcode'][$i]).PHP_EOL.$this->snippets['closebbcode'][$i];

      $this->text = preg_replace('/___SNIPPET___/', PHP_EOL.trim($snippet).PHP_EOL, $this->text, 1);
    }
  }


  //! @brief Replace links.
  protected function replaceLinks() {

    $this->text = preg_replace_callback('%(?i)<a[^>]+>(.+?)</a>%',

      function ($matches) {

        // Extracts the url.
        if (preg_match('/\s*(?i)href\s*=\s*("([^"]*")|\'[^\']*\'|([^\'">\s]+))/', $matches[0], $others) === 1) {
          $href = strtolower(trim($others[1], '"'));

          // Extracts the target.
          if (preg_match('/\s*(?i)target\s*=\s*("([^"]*")|\'[^\']*\'|([^\'">\s]+))/', $matches[0], $others) === 1)
            $target = strtolower(trim($others[1], '"'));
          else
            $target = "_self";
        }
        else
          throw new \RuntimeException(sprintf("Text identified by '%d' has malformed links", $this->id));

        return "[url=".$href." t=".$target."]".$matches[1]."[/url]";

      },

      $this->text
    );

  }


  //! @brief Replace images.
  protected function replaceImages() {
    $this->text = preg_replace_callback('/<img[^>]+>/i',

      function ($matches) {

        // Extracts the src.
        if (preg_match('/\s*(?i)src\s*=\s*("([^"]*")|\'[^\']*\'|([^\'">\s]+))/', $matches[0], $others) === 1)
          $src = strtolower(trim($others[1], '"'));
        else
          throw new \RuntimeException(sprintf("Text identified by '%d' has malformed images", $this->id));

        return "[img]".$src."[/img]";

      },

      $this->text
    );

  }


  //! @brief Replace all other simple tags, even the lists.
  protected function replaceOtherTags() {
    $this->text = preg_replace_callback('%</?[a-z][a-z0-9]*[^<>]*>%i',

      function ($matches) {
        $tag = strtolower($matches[0]);

        switch ($tag) {
          case ($tag == '<strong>' || $tag == '<b>'):
            return '[b]';
            break;

          case ($tag == '</strong>' || $tag == '</b>'):
            return '[/b]';
            break;

          case ($tag == '<em>' || $tag == '<i>'):
            return '[i]';
            break;

          case ($tag == '</em>' || $tag == '</i>'):
            return '[/i]';
            break;

          case '<u>':
            return '[u]';
            break;

          case '</u>':
            return '[/u]';
            break;

          case ($tag == '<strike>' || $tag == '<del>'):
            return '[s]';
            break;

          case ($tag == '</strike>' || $tag == '</del>'):
            return '[/s]';
            break;

          case '<ul>':
            return '[list]';
            break;

          case '</ul>':
            return '[/list]';
            break;

          case '<ol>':
            return '[list=1]';
            break;

          case '</ol>':
            return '[/list]';
            break;

          case '<li>':
            return '[*]';
            break;

          case '</li>':
            return '';
            break;

          case '<center>':
            return '[center]';
            break;

          case '</center>':
            return '[/center]';
            break;

          default:
            return $tag;
        }
      },

      $this->text
    );

  }


  //! @brief Converts the provided HTML text into BBCode.
  public function toBBCode() {
    $this->removeSnippets();
    $this->replaceLinks();
    $this->replaceImages();
    $this->replaceOtherTags();
    $this->text = strip_tags($this->text);
    $this->restoreSnippets();

    return $this->text;
  }

} 