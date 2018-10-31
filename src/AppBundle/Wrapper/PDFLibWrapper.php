<?php

namespace AppBundle\Wrapper;

// Copied from core app

class PDFLibWrapper
{
    private $_xml;

    private $_signature_path;

    private $_file_name;

    private $_general_settings;

    private $_font_family;
    private $_font_size;
    private $_line_spacing;

    private $_current_page_id;
    private $_page_width;
    private $_page_height;

    private $_canvas_width;
    private $_canvas_height;

    private $_lower_left_x;
    private $_lower_left_y;
    private $_upper_right_x;
    private $_upper_right_y;

    private $_temp_last_y;
    private $_last_y;
    private $_last_x;

    private $_table_options;


    static $inch_to_pixel = 70;
    static $cm_to_pixel = 38;


    private function _GetXml()
    {
        return $this->_xml;
    }

    private function _SetXml($_xml)
    {
        $this->_xml = $_xml;
    }

    private function _GetSignaturePath()
    {
        return $this->_signature_path;
    }

    private function _SetSignaturePath($_signature_path)
    {
        $this->_signature_path = $_signature_path;
    }

    private function _GetFileName()
    {
        return $this->_file_name;
    }

    private function _SetFileName($_file_name)
    {
        $this->_file_name = $_file_name;
    }

    /**
     * Returns an arrays with general document settings
     *
     * Array format :
     *
     * Array
     * (
     *   ['paper_size'] => letter
     *   ['font_family'] => verdana
     *   ['font_size'] => 12
     *   ['margin_top'] => 1in
     *   ['margin_right'] => 1in
     *   ['margin_bottom'] => 1in
     *   ['margin_left'] => 1in
     *   ['first_page_top'] => 1in
     * )
     *
     */
    private function _GetGeneralSettings()
    {
        return $this->_general_settings;
    }

    private function _SetGeneralSettings($_general_settings)
    {
        $this->_general_settings = $_general_settings;
    }

    private function _GetFontFamily()
    {
        return $this->_font_family;
    }

    private function _SetFontFamily($_font_family)
    {
        $this->_font_family = $_font_family;
    }

    private function _GetFontSize()
    {
        return $this->_font_size;
    }

    private function _SetFontSize($_font_size)
    {
        $this->_font_size = $_font_size;
    }

    private function _GetLineSpacing()
    {
        return $this->_line_spacing;
    }

    private function _SetLineSpacing($_line_spacing)
    {
        $this->_line_spacing = $_line_spacing;
    }

    private function _GetCurrentPageId()
    {
        return strtolower($this->_current_page_id);
    }

    private function _SetCurrentPageId($_current_page_id)
    {
        $this->_current_page_id = strtolower($_current_page_id);
    }

    private function _GetPageWidth()
    {
        return $this->_page_width;
    }

    private function _SetPageWidth($_page_width)
    {
        $this->_page_width = $_page_width;
    }

    private function _GetPageHeight()
    {
        return $this->_page_height;
    }

    private function _SetPageHeight($_page_height)
    {
        $this->_page_height = $_page_height;
    }

    private function _GetCanvasWidth()
    {
        return $this->_canvas_width;
    }

    private function _SetCanvasWidth($_canvas_width)
    {
        $this->_canvas_width = $_canvas_width;
    }

    private function _GetCanvasHeight()
    {
        return $this->_canvas_height;
    }

    private function _SetCanvasHeight($_canvas_height)
    {
        $this->_canvas_height = $_canvas_height;
    }

    private function _GetLowerLeftX()
    {
        return $this->_lower_left_x;
    }

    private function _SetLowerLeftX($_lower_left_x)
    {
        $this->_lower_left_x = $_lower_left_x;
    }

    private function _GetLowerLeftY()
    {
        return $this->_lower_left_y;
    }

    private function _SetLowerLeftY($_lower_left_y)
    {
        $this->_lower_left_y = $_lower_left_y;
    }

    private function _GetUpperRightX()
    {
        return $this->_upper_right_x;
    }

    private function _SetUpperRightX($_upper_right_x)
    {
        $this->_upper_right_x = $_upper_right_x;
    }

    private function _GetUpperRightY()
    {
        return $this->_upper_right_y;
    }

    private function _SetUpperRightY($_upper_right_y)
    {
        $this->_upper_right_y = $_upper_right_y;
    }

    private function _GetTempLastY()
    {
        return $this->_temp_last_y;
    }

    private function _SetTempLastY($_temp_last_y, $reset = false)
    {
        if($reset)
        {
            $this->_temp_last_y = $_temp_last_y;
        }
        elseif(intval($_temp_last_y) < intval($this->_temp_last_y) && intval($this->_temp_last_y) != 0)
        {
            $this->_temp_last_y = $_temp_last_y;
        }
    }

    private function _GetLastY()
    {
        return $this->_last_y;
    }

    private function _SetLastY($_last_y, $reset=false)
    {
        if($reset == true || intval($_last_y) < intval($this->_last_y))
        {
            $this->_last_y = $_last_y;
            $this->_SetTempLastY($_last_y, true);
        }
    }

    private function _GetLastX()
    {
        return $this->_last_x;
    }

    private function _SetLastX($_last_x)
    {
        $this->_last_x = $_last_x;
    }



    /**
     * Instantiates the pdf object and initializes it
     *
     * @param obj pdf
     * @return void
     */
    private function _SetObj($pdf_obj)
    {
        $this->pdf = $pdf_obj;
    }

    /**
     * Gets the pdf object
     *
     * @return object
     */
    private function _GetObj()
    {
        if (!isset($this->pdf))
        {
            return NULL;
        }
        return $this->pdf;
    }


    public function __construct($file_name, $xml_string, $signature_path, $general_settings = array() )
    {
        $this->_SetFileName($file_name);

        $xml_string = $this->_FormatXMLString($xml_string);

        try
        {
            $this->_SetXml(simplexml_load_string($xml_string));
        }
        catch(Exception $e)
        {
            throw new Exception("We have an xml error" . $e->getMessage() . "Traceback" . $e->getTrace());
        }

        $this->_SetSignaturePath($signature_path);

        // Create the pdf object an initialize
        $form_pdf = new PDFlib();

        $this->_SetObj($form_pdf);

        // Set the error policy to return (stop)
        $this->_GetObj()->set_option('errorpolicy=' . PDF_LIB_RETURN_POLICY);

        // Set the Library License
        $this->_GetObj()->set_option("license=" . PDF_LIB_LICENSE);

        // All strings are expected as utf8.
        $this->_GetObj()->set_option("stringformat=utf8");

        // Open new PDF file with PDF/X 2003 standards.
        // Insert a file name to create the PDF on disk.
        //if($this->_GetObj()->begin_document($this->_GetFileName(), "pdfx=PDF/X-3:2003") == 0)
        if($this->_GetObj()->begin_document($this->_GetFileName(), "pdfx=PDF/X-1a:2001") == 0)
        {
            die("Error: " . $this->_GetObj()->get_errmsg());
        }

        // Load the ISOcoated icc profile that is necessary to generate PDF/X compliant PDF files
        if ($this->_GetObj()->load_iccprofile(PDF_LIB_ICC_PROFILE_PATH, "usage=outputintent") == 0)
        {
            throw new PDFLibException("Error: Please install the ICC profile package from www.pdflib.com to run the PDF/X starter sample.\n");
        }

        $this->_SetGeneralSettings($general_settings);

        // PDF meta data.
        $this->_GetObj()->set_info("Creator", "zlien");
        $this->_GetObj()->set_info("Author", "Scott Wolfe");
        $this->_GetObj()->set_info("Title", "Order Form");

        $this->_GetObj()->set_option("searchpath={" . PDF_LIB_SEARCH_PATH . "}");

        $this->_SetLowerLeftX(0);
        $this->_SetLowerLeftY(0);
    }


    private function _CheckInlineHr($addText)
    {
        // Check if the text has table tags in it
        // It MUST be compares using the === or !== operators,
        // because strpos() may return non Boolean values that can easily evaluate to FALSE
        // if(strpos("/table", $addText) === FALSE)
        if(strpos($addText, '<hr>') !== false ||
            strpos($addText, '<hr/>') !== false ||
            strpos($addText, '<hr />') !== false)
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    private function _CheckInlineTables($addText)
    {
        // Check if the text has table tags in it
        // It MUST be compares using the === or !== operators,
        // because strpos() may return non Boolean values that can easily evaluate to FALSE
        // if(strpos("/table", $addText) === FALSE)
        if (strpos($addText, '/table') !== false)
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    private function _CheckInlineSignature($addText)
    {
        if (strpos($addText, 'signature_holder') !== false)
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    private function _CheckInlineImage($addText)
    {
        if(strpos($addText, '<img') !== false)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function _ExtractTable($addText, $id = "", $start_tag = "<table", $end_tag = "/table>")
    {
        //str - string to search
        //id - text to search for
        //start_tag - start delimiter
        //end_tag - end delimiter

        if($id)
        {
            $pos_srch = strpos($addText, $id);
            //extract string up to id value
            $beg = substr($addText,0, $pos_srch);

            //get position of start delimiter
            $pos_start_tag = strrpos($beg, $start_tag);
        }
        else
        {
            //if no id value get first tag found
            $pos_start_tag = strpos($addText, $start_tag);
        }


        //get position of end delimiter
        $pos_end_tag = strpos($addText, $end_tag, $pos_start_tag);
        //length of end deilimter
        $end_tag_len = strlen($end_tag);
        //length of string to extract
        $len = ($pos_end_tag + $end_tag_len) - $pos_start_tag;
        //Extract the tag
        $tag = substr($addText, $pos_start_tag, $len);

        return $tag;
    }


    //Will remove the table instances and replace it with a delimeter

    private function _RemoveTable($addText, $id = "", $start_tag = "<table" , $end_tag = "/table>")
    {
        //addText - string to search
        //id - text to search for
        //start_tag - start delimiter to remove
        //end_tag - end delimiter to remove

        //find position of tag identifier. loops until all instance of text removed
        while(($pos_srch = strpos($addText, $start_tag))!==false)
        {
            //get text before identifier
            $beg = substr($addText, 0, $pos_srch);
            //get position of start tag
            $pos_start_tag = strrpos($beg, $start_tag);
            //echo 'start: '.$pos_start_tag.'<br>';
            //extract text up to but not including start tag
            $beg = substr($beg, 0, $pos_start_tag);
            //echo "beg: ".$beg."<br>";

            //get text from identifier and on
            $end = substr($addText, $pos_srch);

            //get length of end tag
            $end_tag_len = strlen($end_tag);
            //find position of end tag
            $pos_end_tag = strpos($end,$end_tag);
            //extract after end tag and on
            $end = substr($end,$pos_end_tag+$end_tag_len);

            $addText = $beg . "<split>" . $end;
        }

        //return processed string
        return $addText;
    }


    private function _FormatXMLString($xml_string)
    {
        // $xml_string = str_replace("&nbsp;", " ", $xml_string);
        // $xml_string = str_replace("& ", "&amp;", $xml_string);
        // $xml_string = str_replace("§", "&#167;", $xml_string);
        // $xml_string = str_replace("<br>", "<br />", $xml_string);
        // $xml_string = str_replace("Strong", "strong", $xml_string);
        $xml_string = str_replace("&shy;", "", $xml_string);
        $xml_string = str_replace("&Acirc;", "", $xml_string);
        $xml_string = str_replace("&amp;#", "&#", $xml_string);
        $xml_string = str_replace("&ndash;", "-", $xml_string);
        $xml_string = str_replace("&rlm;", " ", $xml_string);
        $xml_string = str_replace("&rsquo;", "&apos;", $xml_string);
        $xml_string = str_replace(["&eacute;", "&Eacute;"], ["é", "É"], $xml_string);
        $xml_string = str_replace(["&ccedil;", "&Ccedil;"], ["ç", "Ç"], $xml_string);
        $xml_string = str_replace(["&deg;", "&Deg;"], ["°"], $xml_string);

        return $xml_string;
    }

    private function _FormatInlineText($addText, $fontname)
    {
        $addText = html_entity_decode($addText);
        $addText = join("\n", array_map("trim", explode("\n", $addText)));

        if($this->_GetSignaturePath() != "")
        {
            $addText = str_replace("[*signature*]", "<nextparagraph><&signature_holder><&end><nextparagraph>", $addText);
        }
        else
        {
            $addText = str_replace("[*signature*]", "", $addText);
        }

        $addText = str_replace("<strong><em>", "<fontname=" . $fontname . "_Bold_Italic embedding encoding=unicode>", $addText);
        $addText = str_replace("<em><strong>", "<fontname=" . $fontname . "_Bold_Italic embedding encoding=unicode>", $addText);

        $addText = str_replace("<br>", "\n", $addText);
        $addText = str_replace("<br/>", "\n", $addText);
        $addText = str_replace("<br />", "\n", $addText);
        $addText = str_replace("</p>", "\n", $addText);
        //$addText = str_replace(array("&amp;"), array("&"), $addText);

        // Get font tag
        if(strpos($addText, '<font') !== false)
        {
            $font_tag = substr($addText, strpos($addText, '<font'));
            $font_tag = substr($font_tag, 0, strpos($font_tag, '>') + 1);

            // Get font size
            $font_size = substr($font_tag, strpos($font_tag, 'size="'));
            $font_size = str_replace('size="', '', $font_size);
            $font_size = intval(substr($font_size, 0, strpos($font_size, '"')));

            $addText = str_replace($font_tag, "<fontsize=" . $font_size . ">", $addText);
            $block_font_size = $this->_GetFontSize();
            $addText = str_replace("</font>", "<fontsize=" . $block_font_size . ">", $addText);
        }

        $addText = str_replace("<strong>", "<fontname=" . $fontname . "_Bold embedding encoding=unicode>", $addText);
        $addText = str_replace("<b>", "<fontname=" . $fontname . "_Bold embedding encoding=unicode>", $addText);
        $addText = str_replace("<em>", "<fontname=" . $fontname . "_Italic embedding encoding=unicode>", $addText);
        $addText = str_replace("<i>", "<fontname=" . $fontname . "_Italic embedding encoding=unicode>", $addText);


        $addText = str_replace("</em></strong>", "<resetfont>", $addText);
        $addText = str_replace("</strong></em>", "<resetfont>", $addText);
        $addText = str_replace("</em>", "<resetfont>", $addText);
        $addText = str_replace("</i>", "<resetfont>", $addText);
        $addText = str_replace("</b>", "<resetfont>", $addText);
        $addText = str_replace("</strong>", "<resetfont>", $addText);

        $addText = str_replace("<u>", "", $addText);
        $addText = str_replace("</u>", "", $addText);

        return $addText;
    }


    /**
     * Sets table options for a table cells not to be created
     *
     * @return void
     */
    private function _SetTableOptions()
    {
        $this->_table_options = 'fittextline={font='
            . $this->getNormalFontNumber() . ' fontsize='
            . $this->getFontSize() . ' position={'
            . $this->getTableAlignment() . ' center}} margin='
            . $this->getTableDataMargin() . '';
    }

    /**
     * Gets table reference collection
     *
     * @return array
     */
    private function _GetTableOptions()
    {
        if (!isset($this->_table_options))
        {
            return '';
        }
        return $this->_table_options;
    }

    /**
     * Adds table data to the row/cell for a specified table ref number
     *
     * @param string table data
     * @param int row
     * @param int col
     * @param int table reference number
     * @return int
     */
    private function _AddTableCell($tbl_ref, $col, $row, $td)
    {
        $ret = $this->_GetObj()->add_table_cell(
            $tbl_ref,
            $col,
            $row,
            $td,
            $this->_GetTableOptions()
        );
        return $ret;
    }


    /**
     * Creates a table out of an array of data for the PDF
     *
     * @param object pdf
     * @param array data (only two dimensional array will work)
     * @return void
     */
    private function _CreateDataTable($data_array)
    {
        $t = 0;

        $row = 1;

        foreach ($data_array as $key => $value)
        {
            $t = $this->_AddTableCell($t, 1, $row, $key);

            if ($t == 0)
            {
                throw new PDFLibException("Error: " . $this->_GetObj()->get_errmsg());
            }

            $t = $this->_AddTableCell($t, 2, $row, $value);

            if ($t == 0)
            {
                throw new PDFLibException("Error: " . $this->_GetObj()->get_errmsg());
            }

            $row++;
        }
        $this->_CloseTable($t);
    }

    /**
     * Closes out a table by looping through all added table cells and
     * making room in the document for each, with labels and automatic
     * pagination
     *
     * @param table reference number
     * @return void
     */
    private function _CloseTable($table_ref)
    {
        $page = 1;
        $optlist = 'showgrid=false';
        do {


            // Fit the table data onto the current page
            $optlist = 'header=1 fill={{area=rowodd fillcolor={gray 0.9}}} stroke={{line=other}} ';

            $result = $this->_GetObj()->fit_table(
                $table_ref,
                $this->getLowerLeftX(),
                $this->getLowerLeftY(),
                $this->getUpperRightX(),
                $this->getUpperRightY(),
                $optlist
            );
            if ($result ==  '_error') {
                throw new PDFLibException('Couldn\'t place table: ' . $this->_GetObj()->get_errmsg());
            }

        } while ($result == '_boxfull');

        if ($result != '_stop')
        {
            if ($result ==  '_error')
            {
                throw new PDFLibException('Error when placing table: ' . $this->_GetObj()->get_errmsg());
            }
            else
            {
                throw new PDFLibException('User return found in Textflow');
            }
        }

        $this->_GetObj()->delete_table($table_ref, '');
    }


    /**
     * Close the document
     */
    private function _CloseDocument($suspensions_per_page)
    {
        $total_suspensions = 0;
        foreach ($suspensions_per_page as $pagenumber => $suspensions) {
            if($suspensions == 0)
            {
                $total_suspensions++;
            }
            else
            {
                $total_suspensions += $suspensions;
            }



        }

        for ($i = 1; $i <= $total_suspensions; $i++)
        {
            $this->_GetObj()->resume_page("pagenumber=" . $i);
            $this->_GetObj()->end_page_ext("");
        }

        $this->_GetObj()->end_document("");
    }

    private function _GetDimensionsInPixels($unit, $value = 0)
    {
        $unit = strtolower($unit);
        $return_value = 0;

        if(strpos($unit, 'in') !== false)
        {
            $unit = str_replace('in', '', $unit);
            if(is_numeric($unit))
            {
                $return_value = $unit * self::$inch_to_pixel;
            }
        }

        elseif(strpos($unit, 'cm') !== false)
        {
            $unit = str_replace('cm', '', $unit);
            if(is_numeric($unit))
            {
                $return_value = $unit * self::$cm_to_pixel;
            }
        }

        elseif(strpos($unit, 'px') !== false)
        {
            $return_value = str_replace('px', '', $unit);
        }

        elseif(strpos($unit, '%') !== false)
        {
            $unit = str_replace('%', '', $unit);

            if(is_numeric($unit))
            {
                $return_value = ($unit/100) * $value;
            }
        }

        else
        {
            if(is_numeric($unit))
            {
                $return_value = $unit;
            }
        }

        return $return_value;
    }


    /**
     * Render a PDF Document
     *
     * Parses given xml and renders it into a pdf dcocument replacing signatures with images and
     * saves the file in the given file_name.
     *
     *
     * A block is rendered in the page from the bottom left (llx, lly) to the top right (urx, ury) as in the figure :
     * +-------------------------------------------+
     * |             (urx, ury)                    |
     * |     +-----------+                         |
     * |     |           |                         |
     * |     |           |                         |
     * |     |           |                         |
     * |     |           |                         |
     * |     +-----------+                         |
     * | (llx, lly)                                |
     * |                                           |
     * |                                           |
     * |                                           |
     * |                                           |
     * |                                           |
     * |                                           |
     * |                                           |
     * |                                           |
     * |                                           |
     * |                                           |
     * |                                           |
     * |                                           |
     * |                                           |
     * +-------------------------------------------+
     *
     */
    public function RenderPDFDocument()
    {

        // Load xml object
        $page_number = 1;
        $suspended_pages = 0;
        $suspensions_per_page = array();

        foreach($this->_xml->pages as $page_key => $page_value)
        {
            $suspensions_per_page[$page_number] = 0;

            // Set page settings (page_id, margin, default fonts, etc)
            $this->_SetCurrentPageId((string)$page_value->item_id);

            // Get General document settings
            //
            // Array Format :
            //
            // Array
            // (
            //     [paper_size] => letter
            //     [font_family] => verdana
            //     [font_size] => 12
            //     [margin_top] => 1in
            //     [margin_right] => 1in
            //     [margin_bottom] => 1in
            //     [margin_left] => 1in
            //     [first_page_top] => 1in
            // )
            $general_settings = $this->_GetGeneralSettings();

            // Page margins
            $page_margin_left   = 0;
            $page_margin_right  = 0;
            $page_margin_top    = 0;
            $page_margin_bottom = 0;
            $first_page_margin  = 0;

            if(isset($page_value->styles->override_county_styles))
            {
                if(isset($page_value->styles->font_family))
                {
                    $general_settings['font_family'] = (string)$page_value->styles->font_family;
                }
                if(isset($page_value->styles->font_size))
                {
                    $general_settings['font_size'] = (int)$page_value->styles->font_size;
                }
                if(isset($page_value->styles->margin_left))
                {
                    $general_settings['margin_left'] = $page_value->styles->margin_left;
                }
                if(isset($page_value->styles->margin_right))
                {
                    $general_settings['margin_right'] = $page_value->styles->margin_right;
                }
                if(isset($page_value->styles->margin_top))
                {
                    $general_settings['margin_top'] = $page_value->styles->margin_top;
                }
                if(isset($page_value->styles->margin_bottom))
                {
                    $general_settings['margin_bottom'] = $page_value->styles->margin_bottom;
                }

                // Page margins
                $page_margin_left   = $this->_GetDimensionsInPixels($general_settings['margin_left']);
                $page_margin_right  = $this->_GetDimensionsInPixels($general_settings['margin_right']);
                $page_margin_top    = $this->_GetDimensionsInPixels($general_settings['margin_top']);
                $page_margin_bottom = $this->_GetDimensionsInPixels($general_settings['margin_bottom']);

                if($page_number == 1 && isset($page_value->styles->first_page_top))
                {
                    $first_page_margin = intval($page_value->styles->first_page_top);
                }
            }
            else
            {
                // Page margins
                $page_margin_left   = $this->_GetDimensionsInPixels($general_settings['margin_left']) + 5;
                $page_margin_right  = $this->_GetDimensionsInPixels($general_settings['margin_right']) + 5;
                $page_margin_top    = $this->_GetDimensionsInPixels($general_settings['margin_top']) + 5;
                $page_margin_bottom = $this->_GetDimensionsInPixels($general_settings['margin_bottom']) + 5;
                if($page_number == 1)
                {
                    $first_page_margin = intval($this->_GetDimensionsInPixels($general_settings['first_page_top']));
                }
            }

            $this->_SetFontFamily($general_settings['font_family']);
            $this->_SetFontSize($general_settings['font_size']);

            $font = $this->_GetObj()->load_font($this->_GetFontFamily() , "unicode", "embedding");

            // @TODO This needs serious improvement!
            $page_size = $general_settings['paper_size'];

            $paper_sizes_array = array(
                'letter' => array('8.75in', '11in'),
                'legal' => array('8.75in', '14in'),
            );

            switch(strtolower($page_size))
            {
                // If the paper siz is either legal or letter
                // letter dimensions are 8.5" x 11"
                // legal dimensions are 8.5" x 14"
                case 'letter':
                case 'legal':
                    $current_page_width   = $this->_GetDimensionsInPixels($paper_sizes_array[strtolower($page_size)][0]);
                    $current_page_height  = $this->_GetDimensionsInPixels($paper_sizes_array[strtolower($page_size)][1]);

                    $current_canvas_width     = $current_page_width  - ($page_margin_left + $page_margin_right);
                    $current_canvas_height    = $current_page_height - ($page_margin_top + $page_margin_bottom);
                    break;

                // If paper size is not set up correctly assume one
                default:
                    $current_page_width     = '600' - ($page_margin_left + $page_margin_right);
                    $current_page_height    = '800' - ($page_margin_top + $page_margin_bottom);
                    break;
            }

            // Set Page Dimensions
            $this->_SetCanvasWidth($current_canvas_width);
            $this->_SetCanvasHeight($current_canvas_height);

            // Set Canvas Dimensions
            $this->_SetPageWidth($current_page_width);
            $this->_SetPageHeight($current_page_height);


            // Set Canvas outermarks
            $this->_SetLowerLeftX(0 + $page_margin_left);
            $this->_SetLowerLeftY(0 + $page_margin_bottom);
            // $this->_SetUpperRightX($current_page_width - ($page_margin_right));
            // $this->_SetUpperRightY($current_page_height - ($page_margin_top + $first_page_margin));
            $this->_SetUpperRightX($this->_GetLowerLeftX() + $this->_GetCanvasWidth());
            $this->_SetUpperRightY($this->_GetLowerLeftY() + $this->_GetCanvasHeight());


            // $this->_SetTempLastY(intval($current_page_height));
            $this->_SetLastX($page_margin_left);


            // Set Pdf dimensions.
            // Start a page
            // Establish coordinates with the origin in the upper left corner.

            $this->_GetObj()->begin_page_ext(0, 0, "width=" . $page_size . ".width height=" . $page_size . ".height");

            // Load default font and set default font size
            $this->_GetObj()->setfont($font, $this->_GetFontSize());

            // Reset block_row
            $this->_SetLastY($this->_GetUpperRightY());
            $this->_SetLastX($this->_GetLowerLeftX());
            $this->_SetLastY($current_page_height - ($page_margin_top + $first_page_margin), true);

            $row_width_filled = 0;

            // Check if the page has child blocks
            if(isset($page_value->block))
            {
                // Move blocks to an array format for better loop handling.
                $blocks_array = array();
                foreach ($page_value->block as $child_key => $child_value)
                {
                    $blocks_array[] = $child_value;
                }

                // Loop over blocks
                for ($page_block_counter = 0; $page_block_counter < count($blocks_array); $page_block_counter++)
                {
                    if(strcmp(strtolower(trim($blocks_array[$page_block_counter]->parent_id)), strtolower(trim($this->_GetCurrentPageId()))) != 0 )
                    {
                        //         if not the same
                        //             End the page
                        //             Start a new page
                        //             Set the current page_id as the new page_id
                        $this->_SetLowerLeftX($page_margin_left);
                        $this->_SetLowerLeftY(0);

                        $this->_GetObj()->end_page_ext("");

                        $page_number++;

                        continue;
                    }


                    // Trim all extra spaces
                    $addText = trim($blocks_array[$page_block_counter]->html_content);

                    // Add a new line at the end of every block
                    $addText = $addText . "\n";

                    // Replace tags with inline formatting handlers from PDFLib
                    $addText = $this->_FormatInlineText($addText, $blocks_array[$page_block_counter]->styles->font_family);

                    // Prepare options list for text blocks
                    if(intval($blocks_array[$page_block_counter]->styles->line_spacing) == 0 )
                    {
                        $line_spacing = 115;
                    }
                    else
                    {
                        $line_spacing = floatval($blocks_array[$page_block_counter]->styles->line_spacing) * 100;
                    }

                    $fontname =  $blocks_array[$page_block_counter]->styles->font_family;

                    if((string)$blocks_array[$page_block_counter]->styles->font_weight == "bold")
                    {
                        $fontname = $fontname . "_Bold";
                    }
                    $this->_SetFontSize(intval($blocks_array[$page_block_counter]->styles->font_size));
                    $optlist  = " fontname=" . $fontname
                        . " leading=" . $line_spacing . "%"
                        . " embedding fontsize=" . intval($blocks_array[$page_block_counter]->styles->font_size)
                        . " encoding=unicode "
                        . " alignment=" . $blocks_array[$page_block_counter]->styles->text_align
                        . " parindent=0%"
                        . " leftindent=0%"
                        . " macro {"
                        . " signature_holder {matchbox={name=new boxheight="
                        . "    {ascender descender}} } "
                        . " end {matchbox={end}} } "
                    ;


                    // Set block margins
                    $block_margin_top     = floatval($blocks_array[$page_block_counter]->styles->margin_top);
                    $block_margin_left    = floatval($blocks_array[$page_block_counter]->styles->margin_left);
                    $block_margin_bottom  = floatval($blocks_array[$page_block_counter]->styles->margin_bottom);
                    $block_margin_right   = floatval($blocks_array[$page_block_counter]->styles->margin_right);


                    // Block dimenstions
                    $block_width  = floatval($blocks_array[$page_block_counter]->styles->width);
                    $block_height = floatval($blocks_array[$page_block_counter]->styles->height);

                    // Calculating where the block ends on the x axis
                    $item_type = $blocks_array[$page_block_counter]->item_type;
                    $exploded_array = explode('-', $item_type);
                    if(intval($exploded_array[1]) == 0)
                    {
                        $exploded_array[1] = 100;
                    }
                    $block_type_width = floatval($exploded_array[1]);
                    $block_right_end = $this->_GetLastX() + ($block_type_width * $current_canvas_width * 0.01);


                    // A temporary dirty hack to stop overflowing from the right side
                    if ($block_right_end > $current_page_width)
                    {
                        $block_right_end = $current_page_width - $page_margin_right;
                    }
                    // End of dirty hack

                    // Set The current $llx for block start
                    if(intval($this->_GetLastX()) == $page_margin_left)
                    {
                        $llx = $this->_GetLowerLeftX();
                    }
                    else
                    {
                        $llx = $this->_GetLastX();
                    }
                    // Add block margin to the number
                    $llx = $llx + $block_margin_left;

                    // Set the current $lly for block start
                    $lly = $this->_GetLowerLeftY() + $block_margin_bottom;

                    // Set the current $urx for block end
                    $urx = $block_right_end;

                    if(intval($this->_GetLastY()) == 0)
                    {
                        $ury = abs(floatval($current_page_height) - $block_margin_top);
                    }
                    else
                    {
                        // $ury = abs($this->_GetPageHeight() - floatval($this->_GetLastY()) - $margin_top);
                        $ury = abs(floatval($this->_GetLastY()) - $block_margin_top);
                    }

                    // Start Rendering blocks
                    // First check for all special cases

                    // Check if the block has an <hr>
                    if($this->_CheckInlineHr($addText))
                    {
                        $this->_GetObj()->setlinewidth(1.0);

                        // Set the current point for graphics output

                        $this->_GetObj()->moveto($llx, $this->_GetTempLastY() - $block_margin_top);

                        // Draw a line from the current point to the supplied point
                        $this->_GetObj()->lineto($this->_GetUpperRightX() - $block_margin_left, $this->_GetTempLastY() - $block_margin_top);

                        // Stroke the path using the current line width and stroke color
                        $this->_GetObj()->stroke();

                        $this->_SetTempLastY($this->_GetTempLastY() - $block_margin_top - $block_margin_bottom);
                    }

                    // If there is a table process it!
                    elseif($this->_CheckInlineTables($addText))
                    {
                        $splitted = explode("<table", $addText);
                        $fontname =  $blocks_array[$page_block_counter]->styles->font_family;

                        // $ury = $this->_GetTempLastY();

                        // $this->_SetTempLastY($, true);

                        foreach ($splitted as $sub_block) {
                            $tf = 0;
                            if($this->_CheckInlineTables($sub_block))
                            {
                                if(intval($table_ury) == 0)
                                {
                                    $table_ury = $this->_GetTempLastY();
                                }

                                $tbl = 0;
                                $col = 1;
                                $row = 1;

                                $table_html = $this->_ExtractTable($addText);
                                $sub_block = strip_tags($table_html, '<table><tr><td>');

                                $dom = new DOMDocument();

                                // Load the table html  code
                                $html = $dom->loadHTML($sub_block);

                                // The table by its tag name
                                $tables = $dom->getElementsByTagName('table');


                                //get all rows from the table
                                $rows = $tables->item(0)->getElementsByTagName('tr');
                                // get each column by tag name
                                $cols = $rows->item(0)->getElementsByTagName('th');
                                $row_headers = NULL;
                                foreach ($cols as $node) {
                                    $row_headers[] = $node->nodeValue;
                                }

                                $table = array();
                                //get all rows from the table
                                $rows = $tables->item(0)->getElementsByTagName('tr');
                                foreach ($rows as $row)
                                {
                                    // get each column by tag name
                                    $cols = $row->getElementsByTagName('td');
                                    $row = array();
                                    $i = 0;
                                    foreach ($cols as $node) {
                                        if($row_headers==NULL)
                                            $row[] = $node->nodeValue;
                                        else
                                            $row[$row_headers[$i]] = $node->nodeValue;
                                        $i++;
                                    }
                                    $table[] = $row;
                                }


                                $row_id = 1;
                                $first_col_width = 0;
                                $second_col_wdth = 0;
                                foreach($table as $row)
                                {

                                    $cell_optlist = " fittextflow={verticalalign=top}"
                                        . " rowheight=1 "
                                    ;
                                    // Column 1
                                    $tf = 0;
                                    if(isset($row[0]) && !empty($row[0]))
                                    {
                                        $text = $this->_FormatInlineText($row[0], $fontname);
                                    }
                                    $tf = $this->_GetObj()->create_textflow(trim($text), $optlist);
                                    $tbl = $this->_GetObj()->add_table_cell($tbl, 1, $row_id, "", $cell_optlist . " textflow=" . $tf);

                                    // Column 2
                                    $tf = 0;
                                    if(isset($row[1]) && !empty($row[1]))
                                    {
                                        $text = $this->_FormatInlineText($row[1], $fontname);
                                    }
                                    $tf = $this->_GetObj()->create_textflow(trim($text), $optlist);
                                    $tbl = $this->_GetObj()->add_table_cell($tbl, 2, $row_id, "", $cell_optlist . " textflow=" . $tf);

                                    $row_id++;
                                }


                                // Actual rendering of the table
                                $font = $this->_GetObj()->load_font($fontname, "unicode", "embedding");

                                $tab_optlist = "fittextline={font=" . $font
                                    . " fontsize=" . intval($blocks_array[$page_block_counter]->styles->font_size)
                                    . " }";
                                $result = $this->_GetObj()->fit_table($tbl, $llx + 5, $lly + 5, $urx - 5, $table_ury - 5, "");


                                $this->_SetTempLastY($table_ury - intval(10 + $this->_GetObj()->info_table($tbl, "height")));
                            }
                            else
                            {
                                $sub_block = strip_tags($sub_block, '<fontname><resetfont>');
                                $text = $this->_FormatInlineText($sub_block, $fontname);
                                $tf = $this->_GetObj()->create_textflow($text, $optlist);

                                ########################################################
                                # Start of copy code
                                ########################################################
                                if(!isset($suspensions_per_page[$page_number]))
                                {
                                    $suspensions_per_page[$page_number] = 0;
                                }


                                $suspensions = 1;
                                $j = 0;
                                $existing_suspensions = intval($suspensions_per_page[$page_number]);
                                do
                                {
                                    $result = $this->_GetObj()->fit_textflow($tf, $llx, $lly, $urx, $ury, "");
                                    if($result != "_stop")
                                    {

                                        $this->_GetObj()->suspend_page("");

                                        $do_we_have_an_overflow = "_boxfull";

                                        if($existing_suspensions != 0 && $suspensions < $existing_suspensions)
                                        {
                                            $j++;
                                            // print "<p>We are at page " . ($page_number + $j) . "</p>";
                                            $this->_GetObj()->resume_page("pagenumber=" . ($page_number + $j));
                                        }
                                        else
                                        {
                                            $this->_GetObj()->begin_page_ext(0, 0, "width=" . $page_size . ".width height=" . $page_size . ".height");
                                            // print "<p>We are at page " . ($page_number + $suspensions) . " [new]</p>";
                                        }

                                        $ury = $this->_GetUpperRightY();

                                        $suspensions++;

                                    }
                                    $this->_SetTempLastY(floatval($this->_GetObj()->info_textflow($tf, "textendy")));



                                } while ($result != "_stop");

                                if (isset($do_we_have_an_overflow) && strcmp($do_we_have_an_overflow, "_stop"))
                                {
                                    $this->_GetObj()->suspend_page("");
                                    $this->_GetObj()->resume_page("pagenumber=" . $page_number);
                                }


                                if($suspensions > $suspensions_per_page[$page_number])
                                {
                                    $suspensions_per_page[$page_number] = $suspensions;
                                }
                                ########################################################
                                # End of copy code
                                ########################################################

                                $table_ury = floatval($this->_GetObj()->info_textflow($tf, "textendy"));
                            }
                        }
                    }

                    // If there is a signature handle it
                    elseif($this->_CheckInlineSignature($addText))
                    {

                        $sub_tfs = explode("<nextparagraph>", $addText);
                        $x = intval($this->_GetLastX());

                        $i = 0;
                        $len = count($sub_tfs);

                        $this->_SetTempLastY($ury, true);

                        $suspended = false;
                        $suspensions = 1;

                        $j = 0;
                        $existing_suspensions = intval($suspensions_per_page[$page_number]);

                        foreach ($sub_tfs as $sub_tf)
                        {
                            $tf = 0;
                            $force_set_y = true;

                            if($this->_CheckInlineSignature($sub_tf))
                            {
                                $image_size = getimagesize($this->_GetSignaturePath());

                                $signature = $this->_GetObj()->load_image("auto", $this->_GetSignaturePath(), "");

                                $align = "" . $blocks_array[$page_block_counter]->styles->text_align;

                                if ($align == "right")
                                {
                                    $img_llx = $this->_GetUpperRightX() - $image_size[0];
                                }
                                else
                                {
                                    $img_llx = $llx;
                                }

                                $y = intval($this->_GetTempLastY());
                                $img_lly = $y - $image_size[1] - 5;


                                if( (intval($y - $image_size[1])) < $lly )
                                {
                                    $img_lly = $this->_GetUpperRighty() - $image_size[1] - 5;
                                    $this->_GetObj()->suspend_page("");

                                    if($existing_suspensions != 0 && $suspensions < $existing_suspensions)
                                    {
                                        $j++;
                                        // print "<p>We are at page " . ($page_number + $j) . "</p>";
                                        $this->_GetObj()->resume_page("pagenumber=" . ($page_number + $j));
                                    }
                                    else
                                    {
                                        $this->_GetObj()->begin_page_ext(0, 0, "width=" . $page_size . ".width height=" . $page_size . ".height");
                                        // print "<p>We are at page " . ($page_number + $suspensions) . " [new]</p>";
                                    }

                                    $suspensions++;

                                }
                                $this->_GetObj()->fit_image($signature, $img_llx, $img_lly, "fitmethod=meet boxsize={0 " . $image_size[1] . "} position={left bottom} matchbox={name=signature}");
                                // $this->_SetTempLastY(intval($y) - intval($image_size[1]));

                                $this->_GetObj()->close_image($signature);

                                if ($this->_GetObj()->info_matchbox("signature", 1, "exists") == 1)
                                {
                                    $y_after_image = intval($this->_GetObj()->info_matchbox("signature", 1, "y1"));

                                    $this->_SetTempLastY($y_after_image, $force_set_y);
                                }
                            }
                            elseif(!empty($sub_tf))
                            {

                                $tf = $this->_GetObj()->create_textflow(trim($sub_tf), $optlist);

                                ########################################################
                                # Start of copy code
                                ########################################################
                                if(!isset($suspensions_per_page[$page_number]))
                                {
                                    $suspensions_per_page[$page_number] = 1;
                                }


                                $subtf_ury = $this->_GetTempLastY();
                                do
                                {

                                    $result = $this->_GetObj()->fit_textflow($tf, $llx, $lly, $urx, $subtf_ury, "");
                                    if($result != "_stop")
                                    {
                                        $subtf_ury = $this->_GetUpperRighty();
                                        $suspennded = true;

                                        $this->_GetObj()->suspend_page("");

                                        $do_we_have_an_overflow = "_boxfull";

                                        if($existing_suspensions != 0 && $suspensions < $existing_suspensions)
                                        {
                                            $j++;
                                            // print "<p>We are at page " . ($page_number + $j) . "</p>";
                                            $this->_GetObj()->resume_page("pagenumber=" . ($page_number + $j));
                                        }
                                        else
                                        {
                                            $this->_GetObj()->begin_page_ext(0, 0, "width=" . $page_size . ".width height=" . $page_size . ".height");
                                            // print "<p>We are at page " . ($page_number + $suspensions) . " [new]</p>";
                                        }

                                        $ury = $this->_GetUpperRightY();

                                        $suspensions++;

                                    }
                                    $this->_SetTempLastY(floatval($this->_GetObj()->info_textflow($tf, "textendy")));



                                } while ($result != "_stop");





                                ########################################################
                                # End of copy code
                                ########################################################

                                $tf = 0;
                            }
                        }

                        if (isset($do_we_have_an_overflow) && strcmp($do_we_have_an_overflow, "_stop"))
                        {
                            $this->_GetObj()->suspend_page("");
                            $this->_GetObj()->resume_page("pagenumber=" . $page_number);
                        }

                        if($suspensions > $suspensions_per_page[$page_number])
                        {
                            $suspensions_per_page[$page_number] = $suspensions;
                        }

                    }

                    // Find single html <img src="..." /> tag in block (no surrounding text)
                    else if($this->_CheckInlineImage($addText))
                    {
                        $x = intval($this->_GetLastX());
                        $this->_SetTempLastY($ury, true);

                        $suspended = false;
                        $suspensions = 1;

                        $j = 0;
                        $existing_suspensions = intval($suspensions_per_page[$page_number]);

                        // Get image tag
                        $img_tag = substr($addText, strpos($addText, '<img'));
                        $img_tag = substr($img_tag, 0, strpos($img_tag, '/>'));

                        // Get image source (a url)
                        $img_url = substr($img_tag, strpos($img_tag, 'src="'));
                        $img_url = str_replace('src="', '', $img_url);
                        $img_url = substr($img_url, 0, strpos($img_url, '"'));

                        // Get image style (a CSS)
                        $css_width = false;
                        if(strpos($img_tag, 'style="') !== false)
                        {
                            $img_css = substr($img_tag, strpos($img_tag, 'style="'));
                            $img_css = str_replace('style="', '', $img_css);
                            $img_css = substr($img_css, 0, strpos($img_css, '"'));

                            $full_css_styles = explode(';', $img_css);
                            $styles = array();
                            foreach($full_css_styles as $style)
                            {
                                if(empty($style))
                                {
                                    continue;
                                }
                                list($name, $value) = explode(':', $style);
                                $styles[$name] = $value;
                            }
                            if(isset($styles['width']) && isset($styles['height']))
                            {
                                $css_width = true;
                                $image_size = array();
                                $image_size[0] = $this->_GetDimensionsInPixels($styles['width']);
                                $image_size[1] = $this->_GetDimensionsInPixels($styles['height']);
                            }
                        }

                        if(filter_var($img_url, FILTER_VALIDATE_URL) === false)
                        {
                            continue;
                        }

                        $file_headers = @get_headers($img_url);
                        if(strpos($file_headers[0], 'Not Found') !== false ||
                            strpos($file_headers[0], 'Forbidden') !== false ||
                            strpos($file_headers[0], 'Error') !== false)
                        {
                            continue;
                        }
                        $tmpfname = tempnam(sys_get_temp_dir(), 'zlien_pdf');
                        file_put_contents($tmpfname, fopen($img_url, 'r'));

                        if(!$css_width)
                        {
                            $image_size = getimagesize($tmpfname);
                        }

                        $zlien_image = $this->_GetObj()->load_image("auto", $tmpfname, "");

                        $align = "" . $blocks_array[$page_block_counter]->styles->text_align;

                        if ($align == "right")
                        {
                            $img_llx = $this->_GetUpperRightX() - $image_size[0];
                        }
                        else if ($align == "center")
                        {
                            $block_center = $llx + (($this->_GetUpperRightX() - $llx) / 2);
                            $img_llx = intval($block_center - ($image_size[0] / 2));
                        }
                        else
                        {
                            $img_llx = $llx;
                        }

                        $y = intval($this->_GetTempLastY());
                        $img_lly = $y - $image_size[1] - 5;

                        if( (intval($y - $image_size[1])) < $lly )
                        {
                            $img_lly = $this->_GetUpperRighty() - $image_size[1] - 5;
                        }
                        $this->_GetObj()->fit_image($zlien_image, $img_llx, $img_lly, "fitmethod=meet boxsize={" . $image_size[0] . " " . $image_size[1] . "} position={left bottom} matchbox={name=zlien_image}");
                        // $this->_SetTempLastY(intval($y) - intval($image_size[1]));

                        $this->_GetObj()->close_image($zlien_image);

                        if(!isset($blocks_array[$page_block_counter]->styles->float) && $this->_GetObj()->info_matchbox("zlien_image", 1, "exists") == 1)
                        {
                            $y_after_image = intval($this->_GetObj()->info_matchbox("zlien_image", 1, "y1"));

                            $this->_SetTempLastY($y_after_image, true);
                        }
                    }

                    // Else render normally
                    else
                    {
                        $tf = 0;
                        $tf = $this->_GetObj()->create_textflow($addText, $optlist);

                        // $do_we_have_an_overflow = $result = $tihs->_GetObj()->fit_textflow($tf, $llx, $lly, $urx, $ury, " blind");


                        ########################################################
                        # Start of copy code
                        ########################################################
                        if(!isset($suspensions_per_page[$page_number]))
                        {
                            $suspensions_per_page[$page_number] = 0;
                        }


                        $suspensions = 1;
                        $j = 0;
                        $existing_suspensions = intval($suspensions_per_page[$page_number]);
                        do
                        {
                            $result = $this->_GetObj()->fit_textflow($tf, $llx, $lly, $urx, $ury, "");
                            if($result != "_stop")
                            {

                                $this->_GetObj()->suspend_page("");

                                $do_we_have_an_overflow = "_boxfull";

                                if($existing_suspensions != 0 && $suspensions < $existing_suspensions)
                                {
                                    $j++;
                                    // print "<p>We are at page " . ($page_number + $j) . "</p>";
                                    $this->_GetObj()->resume_page("pagenumber=" . ($page_number + $j));
                                }
                                else
                                {
                                    $this->_GetObj()->begin_page_ext(0, 0, "width=" . $page_size . ".width height=" . $page_size . ".height");
                                    // print "<p>We are at page " . ($page_number + $suspensions) . " [new]</p>";
                                }

                                $ury = $this->_GetUpperRightY();

                                $suspensions++;

                            }
                            $this->_SetTempLastY(floatval($this->_GetObj()->info_textflow($tf, "textendy")));



                        } while ($result != "_stop");

                        if (isset($do_we_have_an_overflow) && strcmp($do_we_have_an_overflow, "_stop"))
                        {
                            $this->_GetObj()->suspend_page("");
                            $this->_GetObj()->resume_page("pagenumber=" . $page_number);
                        }


                        if($suspensions > $suspensions_per_page[$page_number])
                        {
                            $suspensions_per_page[$page_number] = $suspensions;
                        }
                        ########################################################
                        # End of copy code
                        ########################################################

                    }

                    // if ($row_width_filled
                    $item_id = $blocks_array[$page_block_counter]->item_id;

                    $row_width_filled = $row_width_filled +  $block_type_width;
                    $this->_SetLastX($row_width_filled * $current_page_width * 0.01);

                    if ($row_width_filled >= 99) {
                        $row_width_filled = 0;
                        $this->_SetLastY($this->_GetTempLastY());
                        $this->_SetLastX($page_margin_left);
                    }

                }
            }

            // Rest $_last_x & $_last_y
            $this->_SetLowerLeftX($page_margin_left);
            $this->_SetLowerLeftY(0);

            // End page
            $this->_GetObj()->suspend_page("");

            $page_number = $page_number + $suspensions_per_page[$page_number];
        }


        // Close document
        $this->_CloseDocument($suspensions_per_page);

        // Finished Rendering
        return true;
    }
}
?>
