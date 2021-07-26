<?php
/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2021
 * @Purpose: File to display html elements
 *
 **************** All functions ****************
 * For further description please go to requested function
 * Variables witch have to be passd through the function are written after the function name inround brackets ().
 *
 * HTML->__construct ( $type [string], $values [array] )
 *
 * HTML->prompt ()
 *
 * HTML->addElement ( $values [array] )
 *
 * HTML->addInput ( $values [array] )
 *
 * HTML->addHeadlineRow ( $values [array] )
 *
 * HTML->addRow ( $values [array] )
 *
 * HTML->addFooterRow ( $values [array] )
 *
 * HTML->addLegendItem ( $values [array] )
 *
 * HTML->addTopNavItem ( $values [array] )
 *
 * HTML->customHTML ( $html [string] )
 */
class HTML {
  // Set variable
  private $type;
  protected $startTag;
  protected $endTag;
  protected $substance;

  /**
   * Creates first element of request
   *
   * Constructor needs $type to generate html start and end tag. $values are special and
   * every $type has other values that needed to be passed. All keys with the ^ symbol are
   * required for proper work of the class. Others are optional.
   */
  function __construct( $type, $values = null ) {
    // Get global variables
    global $url;

    // Set type
    $this->type = $type;

    // Check what type is set
    switch( $type ) {
      /**
       * Start form
       *
       * The following keys must contain $values
       * @string action^
       * @string method [get or post]
       * @string additional [ex. multitype]
       */
      case "form":
        $this->startTag = '<form action="' . $values["action"] . '" method="' . ($values["method"] ?? "get") . '" ' . ($values["additional"] ?? '') . '>';
        $this->endTag = '</form>';
      break;

      /**
       * Start table
       *
       * The following keys must contain $values
       * @string action
       * @string method [get or post]
       * @string additional [ex. multitype]
       */
      case "table":
        $this->startTag = '<table
                              class="rows ' . ($values["classes"] ?? '') . ' ' .
                              ($values["additional"] ?? '') . '">';
        $this->endTag = '</table>';
      break;

      /**
       * Start legend
       *
       * The following keys must contain $values
       * @string classes
       * @string additional [ex. multitype]
       */
      case "legend":
        $this->startTag = '<div
                              class="legend ' .
                              ($values["classes"] ?? '') . '" ' . ($values["additional"] ?? '') . '>';
        $this->endTag = '</div>';
      break;

      /**
       * Start top nav
       *
       * The following keys must contain $values
       * @string classes
       * @string additional
       */
      case "top-nav":
        $this->startTag = '<div class="top-nav ' . ($values["classes"] ?? '') . '" ' . ($values["additional"] ?? '') . '>';
        $this->endTag = '</div>';
      break;

      /**
       * Start top nav
       *
       * The following keys must contain $values
       * @string action^
       * @string method
       * @string classes
       * @string placeholder
       * @string s [search value]
       * @string additional [ex. multitype]
       */
      case "searchbar":
        $this->startTag = '<form
                              action="' . $values["action"] . '"
                              method="' . ($values["method"] ?? "get") . '"
                              class="search ' . ($values["classes"] ?? '') . '"' .
                              ($values["additional"] ?? '') . '>';
          $this->startTag .= '<input type="text" name="s" value ="' . ($values["s"] ?? '') . '" placeholder="' . ($values["placeholder"] ?? '') . '">';
          $this->startTag .= '<button>
                                <img src="' . $url . 'medias/icons/magnifying-glass.svg" />
                              </button>';
        $this->endTag = '</form>';
      break;

      /**
       * Start rightmenu
       *
       * The following keys must contain $values
       * @string classes
       * @string additional
       */
      case "right-menu":
        $this->startTag = '<div
                              class="right-sub-menu ' . ($values["classes"] ?? '') . '" ' .
                              ($values["additional"] ?? '') . '>';
        $this->endTag = '</div>';
      break;
    }
  }

  /**
   * This function generates output and shows it in browser
   *
   * Execute this function after executing all other functions to get a proper result.
   * Do not execute this function multiple time for the same class or it will occure
   * problems while submiting a form or others.
   */
  public function prompt() {
    // Echo html
    echo $this->startTag;
      echo $this->substance;
    echo $this->endTag;
  }

  /**
   * This function adds a new element to the substance
   *
   * This function can be used for every construct type and will add the correct html
   * element. Use for $values an array with the containg keys that are listet below on
   * the type you request. All keys with the ^ symbol are required for proper work of
   * the class. Others are optional.
   */
  public function addElement( $values ) {
    switch( $this->type ) {
      /**
       * This function adds a new input item
       */
      case "form":
        /**
         * The values depends on the type of the input. For more informations about
         * the $values go to the addInput function at the possition of your requested
         * key.
         */
        $this->addInput( $values );
      break;

      /**
       * This function adds a new table row
       */
      case "table":
        /**
         * Use for this subsection a multidimensional array what contains at least
         * one of the following keys: headline, row, footer. The value of the key are
         * the required values
         */
        if( isset($values["headline"]) ) {
          /**
           * Custom values
           * @string additional
           * @array items^ [Multidimensional array]
           *    @string context
           *    @string additional
           */
          $this->addHeadlineRow( $values["headline"] );
        }

        if( isset($values["row"]) ) {
          /**
           * Custom values
           * @string additional
           * @array items^ [Multidimensional array]
           *    @string context
           *    @string additional
           */
          $this->addRow( $values["row"] );
        }

        if( isset($values["footer"]) ) {
          /**
           * Custom values
           * @string context^
           * @string classes
           * @string additional
           */
          $this->addFooterRow( $values["footer"] );
        }
      break;

      /**
       * This function adds a new legend input
       */
      case "legend":
        /**
         * Custom values
         * @string bcolor^
         * @string title^
         * @string classes
         * @string additional
         */
        $this->addLegendItem( $values );
      break;

      /**
       * This function adds a new top nav item
       */
      case "top-nav":
        /**
         * Custom values
         * @string context^
         * @string link
         * @string additional
         */
        $this->addTopNavItem( $values );
      break;

      /**
       * This function adds a new right menu item
       */
      case "right-menu":
      /**
       * custom values
       * @string classes
       * @string additional
       * @string context^
       * @array dropdown [Multidimensional]
       *   @string classes
       *   @string additional
       *   @string context
       * @string dropdown_classes (only if dropdown is set)
       * @string dropdown_additional (only if dropdown is set)
       */
        $this->addRightMenuItem( $values );
      break;

      /**
       * This function adds new html
       */
      default:
        /**
         * $values is plain html or text and is appended to the executed code
         */
        $this->customHTML( $values );
      break;
    }
  }

  /**
   * This function adds an input to your html form
   *
   * If you execute this function you need to set the type of the class to
   * form concluding required parameters. Use for $values an array with the
   * containg keys that are listet below on the type you request. All keys
   * with the ^ symbol are required for proper work of the class. Others are
   * optional.
   */
  public function addInput( $values ) {
    // Get global variables
    global $url;

    switch( $values["type"] ) {
      /**
       * checkbox input
       *
       * The following keys must contain $values
       * @string type^
       * @string name^
       * @string value^
       * @string checked
       * @string context
       * @string additional_div
       * @boolen disabled
       * @boolen required
       * @string classes
       * @string additional [ex. id]
       */
      case "checkbox":
        $this->substance .= '<label class="checkbox ' . ($values["classes"] ?? '') . '" ' . ($values["additional"] ?? '') . '>';
          $this->substance .= '<input type="checkbox"
              name="' . $values["name"] . '"
              value="' . $values["value"] . '" ' .
              (($values["checked"] ?? false) === true ? "checked" : '') . ' ' .
              (($values["disabled"] ?? false) === true ? "disabled" : '') . ' ' .
              (($values["required"] ?? false) === true ? "required" : '')  .' />';
          $this->substance .= '<div class="checkbox-btn" ' . ($values["additional_div"] ?? '') . '></div>' . ($values["context"] ?? '');
        $this->substance .= '</label>';
      break;

      /**
       * radio input
       *
       * The following keys must contain $values
       * @string type^
       * @string name^
       * @string value^
       * @string checked
       * @string context
       * @boolen disabled
       * @boolen required
       * @string classes
       * @string multiple [ex. id]
       */
      case "radio":
        $this->substance .= '<label class="radio ' . ($values["classes"] ?? '') . '" ' . ($values["multiple"] ?? '') . '>';
          $this->substance .= '<input type="radio"
              name="' . $values["name"] . '"
              value="' . $values["value"] . '" ' .
              (($values["checked"] ?? false) === true ? "checked" : '') . ' ' .
              (($values["disabled"] ?? false) === true ? "disabled" : '') . ' ' .
              (($values["required"] ?? false) === true ? "required" : '')  .' />';
          $this->substance .= '<div></div>' . ($values["context"] ?? '');
        $this->substance .= '</label>';
      break;

      /**
       * Select input
       *
       * The following keys must contain $values
       * @string type^
       * @string name^
       * @string value
       * @array options^
       * @string headline
       * @boolen disabled
       * @boolen required
       * @string classes
       * @string multiple [ex. id]
       * @string custom_options
       */
      case "select":
          $this->substance .= '<div class="select ' . ($values["classes"] ?? '') . '" onclick="toggleOptions(this)" ' . ($values["multiple"] ?? '') . '>';
            $this->substance .= '<input type="text" class="selectValue"
            name="' . $values["name"]  . '" ' .
            (isset($values["value"]) ? 'value="' . $values["value"] . '"' : '') . ' ' .
            (($values["disabled"] ?? false) === true ? "disabled" : '') . ' ' .
            (($values["required"] ?? false) === true ? "required" : '') . '>';
            $this->substance .= '<span class="headline">' . ($values["headline"] ?? $values["value"] ?? '') .'</span>';

            // Set options
            $i = 0;
            $this->substance .= '<div class="options">';
              foreach( $values["options"] as $key => $option) {
                $this->substance .= '<span data-value="' . ($key ?? $i) . '" onclick="selectElement(this)">' . ($option ?? '') . '</span>';

                $i++;
              }
              $this->substance .= ($values["custom_options"] ?? '');
            $this->substance .= '</div>';
          $this->substance .= '</div>';
      break;

      /**
       * Textarea
       *
       * The following keys must contain $values
       * @string name^
       * @string value^
       * @string placeholder^
       * @string rows
       * @string cols
       * @boolen disabled
       * @boolen required
       * @string classes
       * @string additional
       */
      case "textarea":
        $this->substance .=  '<label class="txt-input" ' . ($values["classes"] ?? '') . '"  ' . ($values["additional"] ?? '') . '>';
          $this->substance .=  '<textarea
          name="' . $values["name"] . '" ' .
          (isset($values["rows"]) ? "rows='" . $values["rows"] . "'" : '') .
          (isset($values["cols"]) ? "rows='" . $values["cols"] . "'" : '') .
          (($values["disabled"] ?? false) === true ? "disabled" : '') .' ' .
          (($values["required"] ?? false) === true ? "required" : '') . '/>' .
            ($values["value"] ?? '') .
          '</textarea>';
          $this->substance .=  '<span class="placeholder">' . $values["placeholder"] . '</span>';
        $this->substance .=  '</label>';
      break;

      /**
       * Image
       *
       * @string classes
       * @string additional
       * @string headline^
       * @string name^
       * @string select_info^
       * @boolen disabled
       * @string classes_label
       * @string additional_label
       * @string preview_image
       */
      case "image":
        $this->substance .= '<span
                              class="file-info ' .
                              ($values["classes"] ?? '') . '" ' .
                              ($values["additional"] ?? '') . '>' . $values["headline"] . '</span>';
        $this->substance .= '<label
                              class="file-input ' .
                              ((isset($values["disabled"]) && $values["disabled"]) ? 'disabled' : '' ) . ' ' .
                              ($values["classes_label"] ?? '') . '" ' .
                              ((isset($values["disabled"]) && $values["disabled"]) ? '' : 'onclick="MediaHub.window.open( this.closest(\'form\'), \'logo_fileID\' )"') .
                              ($values["additional_label"] ?? '') . '>';
            $this->substance .= '<div
                                  class="preview-image"
                                  style="background-image: url(\'' . ($values["preview_image"] ?? $url . 'medias/store/favicon-color-512.png') . '\')">
                                </div>';
            $this->substance .= '<input
                                    type="hidden"
                                    name="' . $values["name"] . '"
                                    onchange="MediaHubSelected(this)" value="' . ($values["value"] ?? '') . '">'; // Input for mediahub support
          $this->substance .= '<div class="draganddrop">' . $values["select_info"] .'</div>';
        $this->substance .= '</label>';
      break;

      /**
       * Button
       *
       * The following keys must contain $values
       * @string type^
       * @string name^
       * @string value^
       * @boolen disabled
       * @string additional
       */
      case "button":
        $this->substance .= '<input type="submit"
            name="' . $values["name"] . '"
            value="' . $values["value"] . '" ' .
            (($values["disabled"] ?? false) === true ? "disabled" : '') . ' ' .
            ($values["additional"] ?? '') . '/>';
      break;

      /**
       * Default or for text input
       *
       * The following keys must contain $values
       * @string type^
       * @string name^
       * @string value
       * @string placeholder^
       * @string unit
       * @string regex
       * @boolen disabled
       * @boolen required
       * @string classes
       * @string additional [ex. id]
       */
      default:
        $this->substance .= '<label class="txt-input ' . ($values["classes"] ?? '') . '"  ' . ($values["additional"] ?? '') . '>';
          $this->substance .= '<input
              type="' . $values["type"] . '"
              name="' . $values["name"] . '" ' .
              (isset($values["value"]) ? 'value="' . $values["value"] . '"' : "") . ' ' .
              (isset($values["regex"]) ? 'pattern="' . $values["regex"] . '"' : "") . ' ' .
              (($values["disabled"] ?? false) === true ? "disabled" : '') .' ' .
              (($values["required"] ?? false) === true ? "required" : '') . '/>';
          $this->substance .= '<span class="placeholder">' . $values["placeholder"] . '</span>';
          $this->substance .= (isset($values["unit"]) ? '<span class="unit">' . $values["unit"] . '</span>' : "");
        $this->substance .= '</label>';
      break;
    }
  }

  /**
  * This function adds an headine to your table
  *
  * If you execute this function you need to set the type of the class to
  * table concluding required parameters. Use for $values an array with the
  * containg keys that are listet below on the type you request. All keys
  * with the ^ symbol are required for proper work of the class. Others are
  * optional.
   */
  public function addHeadlineRow( $values ) {
    /**
     * Table headline
     *
     * @string additional
     * @array items^ [Multidimensional array]
     *    @string context
     *    @string additional
     */
    $this->substance .= '<tr ' . ($values["additional"] ?? '') . '>';
      foreach( $values["items"] as $item ) {
        $this->substance .= '<th ' . ($item["additional"] ?? '') . '>' . $item["context"] . '</th>';
      }
    $this->substance .= '</tr>';
  }

  /**
  * This function adds an headine to your table
  *
  * If you execute this function you need to set the type of the class to
  * table concluding required parameters. Use for $values an array with the
  * containg keys that are listet below on the type you request. All keys
  * with the ^ symbol are required for proper work of the class. Others are
  * optional.
   */
  public function addRow( $values ) {
    /**
     * Table headline
     *
     * @string additional
     * @array items^ [Multidimensional array]
     *    @string context
     *    @string additional
     */

    $this->substance .= '<tr ' . ($values["additional"] ?? '') . '>';
      foreach( $values["items"] as $item ) {
        $this->substance .= '<td ' . ($item["additional"] ?? '') . '>' . $item["context"] . '</td>';
      }
    $this->substance .= '</tr>';
  }

  /**
  * This function adds an headine to your table
  *
  * If you execute this function you need to set the type of the class to
  * table concluding required parameters. Use for $values an array with the
  * containg keys that are listet below on the type you request. All keys
  * with the ^ symbol are required for proper work of the class. Others are
  * optional.
   */
  public function addFooterRow( $values ) {
    /**
     * Footer nav
     *
     * @string context^
     * @string classes
     * @string additional
     */
    $columns = (substr_count( $this->substance, "th") / 2);

    $this->substance .= '<tr
                            class="nav ' . ($values["classes"]  ?? '') . '" ' .
                            ($values["additional"] ?? '') . '>';
      $this->substance .= '<td colspan="' . $columns . '">';
        $this->substance .= $values["context"];
      $this->substance .= '</td>';
    $this->substance .= '</tr>';
  }

  /**
   * This function adds a legend item to your legend
   *
   * If you execute this function you need to set the type of the class to
   * legend (no parameters required). Use for $values an array with the containing
   * keys that are listet below. All keys with the ^ symbol are required for
   * proper work of the class. Others are optional.
   */
  public function addLegendItem( $values ) {
    /**
     * Legend item
     *
     * @string bcolor^
     * @string title^
     * @string classes
     * @string additional
     */
    $this->substance .= '<div class="legend-element ' . ($values["classes"] ?? '') . ' ' .
                          ($values["additional"] ?? '') . '">';
      $this->substance .= '<div class="legend-button" style="background-color: ' . $values["bcolor"] . '"></div>';
      $this->substance .= ($values["title"] ?? '');
    $this->substance .= '</div>';
  }

  /**
   * This function adds a top menu item to your topmenu
   *
   * If you execute this function you need to set the type of the class to
   * top-nav (no parameters required). Use for $values an array with the containing
   * keys that are listet below. All keys with the ^ symbol are required for
   * proper work of the class. Others are optional.
   */
  public function addTopNavItem( $values ) {
    /**
     * Top Nav Menu Item
     *
     * @string context^
     * @string link
     * @string additional
     */
    $this->substance .= '<a href="' . ($values["link"] ?? '') . '" ' . ($values["additional"] ?? '') . '>' . $values["context"] . '</a>';
  }

  /**
   * This function adds a right menu item to your rightmenu
   *
   * If you execute this function you need to set the type of the class to
   * right-menu (no parameters required). Use for $values an array with the containing
   * keys that are listet below. All keys with the ^ symbol are required for
   * proper work of the class. Others are optional.
   */
  public function addRightMenuItem( $values ) {
    /**
     * Right Menu Item
     *
     * @string classes
     * @string additional
     * @string context^
     * @string classes_item
     * @string additional_item
     * @array dropdown [Multidimensional]
     *   @string classes
     *   @string additional
     *   @string context
     * @string dropdown_classes (only if dropdown is set)
     * @string dropdown_additional (only if dropdown is set)
     */

    $this->substance .= '<div
                          class="right-menu-container ' . ($values["classes"] ?? '') . '" ' .
                          ($values["additional"] ?? '') . '>';

      // General infos
      $this->substance .= '<a
                            class="right-menu-item ' .
                            ($values["classes_item"] ?? '') . '" ' .
                            ($values["additional_item"] ?? '') .  '>';
        $this->substance .= $values["context"];
      $this->substance .= '</a>';

      // advanced dropdown
      if( isset($values["dropdown"]) && // Check if dropdown required
          is_array($values["dropdown"]) && // Check if array
          is_array( $values["dropdown"][ array_keys($values["dropdown"])[0] ] ) // Check for multidimensional array
         ) {
        $this->substance .= '<div
                              class="right-sub-menu-container ' . ($values["dropdown_classes"] ?? '') . '" ' .
                              ($values["dropwond_additional"] ?? '')  . '>';

        foreach( $values["dropdown"] as $dropdown) {
          $this->substance .= '<div
                                class="right-sub-menu-item' . ($dropdown["classes"] ?? '') . '"' .
                                ($dropdown["additional"] ?? '') . '>';
            $this->substance .= $dropdown["context"];
          $this->substance .= '</div>';
        }

        $this->substance .= '</div>';
      }
    $this->substance .= '</div>';
  }

  /**
   * Adds custom html
   *
   * If you execute this function you can add custom HTML or plain text and aswell
   * scripts to your HTML.
   *
   */
  public function customHTML( $html ) {
    $this->substance .= $html;
  }
}
 ?>
