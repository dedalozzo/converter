<?php

//! @file BBCodeConverter.php
//! @brief This file contains the BBCodeConverter class.
//! @details
//! @author Filippo F. Fadda


namespace Converter;


//! @brief A rudimental converter that takes as input a BBCode formatted text and converts it to Markdown.
class BBCodeConverter extends Converter {


  //! @brief Replaces BBCode bold.
  protected function replaceBold() {

    $this->text = preg_replace_callback('%\[b\]([\W\D\w\s]*?)\[/b\]%i',

      function ($matches) {
        return "**".$matches[1]."**";
      },

      $this->text
    );

  }


  //! @brief Replaces BBCode italic.
  protected function replaceItalic() {

    $this->text = preg_replace_callback('%\[i\]([\W\D\w\s]*?)\[/i\]%i',

      function ($matches) {
        return "*".$matches[1]."*";
      },

      $this->text
    );

  }


  //! @brief Replaces BBCode underline. Unfortunately Markdown doesn't support underline, so we just revert to normal 
  //! text.
  protected function replaceUnderline() {

    $this->text = preg_replace_callback('%\[u\]([\W\D\w\s]*?)\[/u\]%i',

      function ($matches) {
        return $matches[1];
      },

      $this->text
    );
    
  }


  //! @brief Replaces BBCode strikethrough.
  protected function replaceStrikethrough() {

    $this->text = preg_replace_callback('%\[s\]([\W\D\w\s]*?)\[/s\]%i',

      function ($matches) {
        return "~~".$matches[1]."~~";
      },

      $this->text
    );

  }


  //! @brief Replaces BBCode lists.
  protected function replaceLists() {
    
    $this->text = preg_replace_callback('%\[list(?P<type>=1)?\](?P<items>[\W\D\w\s]*?)\[/list\]%i',

      function ($matches) {
        $buffer = "";

        if (preg_match_all('/(?:(?![[*\]]).)*/i', $matches['items'], $listItems)) {

          if (isset($matches['type']) && $matches['type'] == '=1') { // ordered list
            foreach ($listItems as $itemMatch)
              $buffer .= '- '.trim($itemMatch[1]).PHP_EOL;
          }
          else { // unordered list
            $counter = count($listItems);
            for ($i = 0; $i < $counter; $i++)
              $buffer .= (string)($i + 1).'. '.trim($listItems[$i][1]).PHP_EOL;
          }

        }

        return $buffer;
      },

      $this->text
    );

  }


  //! @brief Replaces BBCode urls.
  protected function replaceUrls() {
    
    $this->text = preg_replace_callback('%\[url\s*=\s*("(?:[^"]*")|\A[^\']*\Z|(?:[^\'">\s]+))\s*(?:[^]\s]*)\]([\W\D\w\s]*?)\[/url\]%i',

      function ($matches) {
        if (isset($matches[1]) && isset($matches[2]))
          return "[".$matches[2]."](".$matches[1].")";
        else
          throw new \RuntimeException(sprintf("Text identified by '%d' has malformed BBCode urls", $this->id));
      },

      $this->text
    );

  }


  //! @brief Replaces BBCode images.
  protected function replaceImages() {

    $this->text = preg_replace_callback('%\[img\s*\]\s*("(?:[^"]*")|\A[^\']*\Z|(?:[^\'">\s]+))\s*(?:[^]\s]*)\[/img\]%i',

      function ($matches) {
        if (isset($matches[1]))
          return "![]"."(".$matches[1].")";
        else
          throw new \RuntimeException(sprintf("Text identified by '%d' have malformed BBCode images", $this->id));
      },

      $this->text
    );

  }


  //! @brief Replaces BBCode snippets.
  protected function replaceSnippets() {

    $this->text = preg_replace_callback('%\[code\s*=?(?P<language>\w*)\](?P<snippet>[\W\D\w\s]*?)\[/code\]%i',

      function ($matches) {
        if (isset($matches['snippet']))
          return "```".$matches['language'].PHP_EOL.$matches['snippet']."```".PHP_EOL;
        else
          throw new \RuntimeException(sprintf("Text identified by '%d' has malformed BBCode snippet", $this->id));
      },

      $this->text
    );

  }


  //! @brief Converts the provided BBCode text to an equivalent Markdown text.
  public function toMarkdown() {
    $this->replaceBold();
    $this->replaceItalic();
    $this->replaceUnderline();
    $this->replaceStrikethrough();
    $this->replaceLists();
    $this->replaceUrls();
    $this->replaceImages();
    $this->replaceSnippets();

    return $this->text;
  }

} 