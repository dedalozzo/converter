<?php

/**
 * @file Converter.php
 * @brief This file contains the Converter class.
 * @details
 * @author Filippo F. Fadda
 */


/**
 * @brief This namespace contains all the converters.
 */
namespace Converter;

/**
 * @brief This is an abstract converter.
 */
abstract class Converter {
  protected $text;
  protected $id;


  /**
   * @brief Constructor.
   * @param[in] string $text The text to be converted.
   * @param[in] string $id You can provide an identifier which is used in case an exception is raised during the
   * conversion process.
   */
  public function __construct($text, $id = '') {
    $this->text = $text;
    $this->id = (string)$id;
  }

}