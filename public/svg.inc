<?php

class svg {

    public $border_width  = 50; // number of pixels before and after the pixel boxes
    public $border_height = 30;  // number of pixel rows above and below the bit boxes
    public $bit_width     = 60;  // width of the box representing a bit
    public $bit_height    = 50;  // height of the box representing a bit
    public $tik_len       = 5;   // length of line that marks the end of a bit (in pixel)
    public $res_val_hight = 15;  // height of text in pixel (reset values, field names
    public $width         = 0;   // number of pixels in each row in this picture
    public $height        = 0;   // number of pixel rows in this picture
    public $size          = 0;   // number of bits in this register (8..64)
    public $rows          = 0;   // number of rows of pixel boxes (= size/16)

    //   64 bit:     32bit:     16bit:
    //   $rows=4    $rows=2     $rows=1
    //   XXXXXXX    XXXXXXX     XXXXXXX
    //   X     X    X     X     X     X
    //   XXXXXXX    XXXXXXX     XXXXXXX
    //   X     X    X     X
    //   XXXXXXX    XXXXXXX
    //   X     X
    //   XXXXXXX
    //   X     X              ' ' = row of bit data
    //   XXXXXXX               X  = border area


    function add_reg_name($name) {
        // name of register
        echo("  <text x=\"" . $this->border_width  . "\" y=\"10\" font-size=\"10\">" . $name . "</text>\n");
    }

    function add_reg_access_mode($access) {
        // access mode (read only / write only / read-write
        echo("  <text x=\"" . $this->width - 30 - $this->border_width . "\" y=\"10\" font-size=\"10\">" . $access . "</text>\n");
    }

    function drawBox($x1, $x2, $y1, $y2, $thickness) {
        // +--------+ y1
        // |        |
        // +--------+ y2
        // x1      x2
        // top line
        echo("  <line id=\"top\" x1=\"" . $x1 . "\" y1=\"" . $y1
                           . "\" x2=\"" . $x2 . "\" y2=\"" . $y1
            . "\" stroke=\"black\" stroke-width=\"" . $thickness . "\" />\n");
        // bottom line
        echo("  <line id=\"bottom\" x1=\"" . $x1 . "\" y1=\"" . $y2
                              . "\" x2=\"" . $x2 . "\" y2=\"" . $y2
            . "\" stroke=\"black\" stroke-width=\"" . $thickness . "\" />\n");
        // right line
        echo("  <line id=\"right\" x1=\"" . $x2 . "\" y1=\"" . $y1
                             . "\" x2=\"" . $x2 . "\" y2=\"" . $y2
            . "\" stroke=\"black\" stroke-width=\"" . $thickness . "\" />\n");
        // left line
        echo("  <line id=\"left\" x1=\"" . $x1 . "\" y1=\"" . $y1
                            . "\" x2=\"" . $x1 . "\" y2=\"" . $y2
            . "\" stroke=\"black\" stroke-width=\"" . $thickness . "\" />\n");
    }

    function add_box() {
        //             x1                   x2                                  y1                                                    y2
        $this->drawBox($this->border_width, $this->width - $this->border_width, $this->border_height,                                 $this->border_height + $this->bit_height,       2);
        if($this->size > 16) {
        $this->drawBox($this->border_width, $this->width - $this->border_width, (2 * $this->border_height) + $this->bit_height,       2 * ($this->border_height + $this->bit_height), 2);
        }
        if($this->size > 32) {
        $this->drawBox($this->border_width, $this->width - $this->border_width, (3 * $this->border_height) + ($this->bit_height * 2), 3 * ($this->border_height + $this->bit_height), 2);
        $this->drawBox($this->border_width, $this->width - $this->border_width, (4 * $this->border_height) + ($this->bit_height * 3), 4 * ($this->border_height + $this->bit_height), 2);
        }
    }

    function add_bit_labels() {
        // the bit ticks and bit labels are located in the border area above the row
        // bit ticks
        for($r = 0; $r < $this->rows; $r++) {
            for($i = 0; $i <= $this->size; $i++) {
                $x = $this->width - $this->border_width - ($i * $this->bit_width);
                $len = $this->tik_len;
                if(0 == $i%4) {
                    $len = $this->tik_len * 2;
                }
                echo("  <line id=\"tick" . $r . "_" . $i . "\" x1=\"" . $x . "\" y1=\"" . ($this->border_height + $r * ($this->border_height + $this->bit_height)) - $len
                                                         . "\" x2=\"" . $x . "\" y2=\"" . ($this->border_height + $r * ($this->border_height + $this->bit_height))
                    . "\" stroke=\"black\" stroke-width=\"1\" />\n");
            }
        }

        // bit numbers
        if($this->size > 32) {
            // four boxes
            for($i = 0; $i < 16; $i++) {
                $x = $this->width - $this->border_width - ($i * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->border_height - 3 + ($this->border_height + $this->bit_height) * 3
                    . "\"  font-size=\"10\">" . $i . "</text>\n");
            }
            for($i = 16; $i < 32; $i++) {
                $x = $this->width - $this->border_width - (($i - 16) * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->border_height - 3 + ($this->border_height + $this->bit_height) * 2
                    . "\"  font-size=\"10\">" . $i . "</text>\n");
            }
            for($i = 32; $i < 48; $i++) {
                $x = $this->width - $this->border_width - (($i - 32) * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->border_height - 3 + ($this->border_height + $this->bit_height)
                    . "\"  font-size=\"10\">" . $i . "</text>\n");
            }
            for($i = 48; $i < $this->size; $i++) {
                $x = $this->width - $this->border_width - (($i - 48) * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->border_height - 3
                    . "\"  font-size=\"10\">" . $i . "</text>\n");
            }
        }else  if($this->size > 16) {
            // two boxes
            for($i = 0; $i < 16; $i++) {
                $x = $this->width - $this->border_width - ($i * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->border_height - 3 + ($this->border_height + $this->bit_height)
                    . "\"  font-size=\"10\">" . $i . "</text>\n");
            }
            for($i = 16; $i < $this->size; $i++) {
                $x = $this->width - $this->border_width - (($i - 16) * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->border_height - 3
                    . "\"  font-size=\"10\">" . $i . "</text>\n");
            }
        } else {
            // one box
            for($i = 0; $i < $this->size; $i++) {
                $x = $this->width - $this->border_width - ($i * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->border_height - 3
                    . "\"  font-size=\"10\">" . $i . "</text>\n");
            }
        }
    }

    function add_reset_value($res_val) {
        // The reset values live in the bottom $$this->res_val_hight pixel rows of the row area.
        // The label "reset to" lives in the border area before the row area.
        // dividing Line
        echo("  <line id=\"res_val\" x1=\"" . $this->border_width .                "\" y1=\"" . $this->height - ($this->border_height) - $this->res_val_hight
                               . "\" x2=\"" . $this->width - $this->border_width . "\" y2=\"" . $this->height - ($this->border_height) - $this->res_val_hight
            . "\" stroke=\"black\" stroke-width=\"1\" />\n");
        if($this->size > 16) {
            // we need two lines
            echo("  <line id=\"res_val\" x1=\"" . $this->border_width                . "\" y1=\"" . ($this->height) - 2 * $this->border_height - $this->bit_height - $this->res_val_hight
                                   . "\" x2=\"" . $this->width - $this->border_width . "\" y2=\"" . ($this->height) - 2 * $this->border_height - $this->bit_height - $this->res_val_hight
                . "\" stroke=\"black\" stroke-width=\"1\" />\n");
        }
        if($this->size > 32) {
            // we need four lines
            echo("  <line id=\"res_val\" x1=\"" . $this->border_width .                "\" y1=\"" . ($this->height) - 3 * $this->border_height - 2 * $this->bit_height - $this->res_val_hight
                                   . "\" x2=\"" . $this->width - $this->border_width . "\" y2=\"" . ($this->height) - 3 * $this->border_height - 2 * $this->bit_height - $this->res_val_hight
                . "\" stroke=\"black\" stroke-width=\"1\" />\n");
            echo("  <line id=\"res_val\" x1=\"" . $this->border_width                . "\" y1=\"" . ($this->height) - 4 * $this->border_height - 3 * $this->bit_height - $this->res_val_hight
                                   . "\" x2=\"" . $this->width - $this->border_width . "\" y2=\"" . ($this->height) - 4 * $this->border_height - 3 * $this->bit_height - $this->res_val_hight
                . "\" stroke=\"black\" stroke-width=\"1\" />\n");
        }

        // Label for Reset Value
        echo("  <text x=\"0\" y=\"" . $this->height - ($this->border_height) - 3 . "\" font-size=\"10\">Resets to</text>\n");
        if($this->size > 16) {
            // we need two labels
            echo("  <text x=\"0\" y=\"" . $this->height - 2 * $this->border_height - $this->bit_height - 3 . "\" font-size=\"10\">Resets to</text>\n");
        }
        if($this->size > 32) {
            // we need four labels
            echo("  <text x=\"0\" y=\"" . $this->height - 3 * $this->border_height - 2 * $this->bit_height - 3 . "\" font-size=\"10\">Resets to</text>\n");
            echo("  <text x=\"0\" y=\"" . $this->height - 4 * $this->border_height - 3 * $this->bit_height - 3 . "\" font-size=\"10\">Resets to</text>\n");
        }

        // Reset Value Numbers
        if($this->size > 32) {
            for($i = 0; $i < 16; $i++) {
                $v = (int)$res_val%2;
                if($res_val > 1) {
                    $res_val = (int)$res_val/2;
                } else {
                    $res_val = 0;
                }
                $x = $this->width - $this->border_width - ($i * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->height - ($this->border_height) - 3
                    . "\"  font-size=\"10\">" . $v . "</text>\n");
            }
            for($i = 16; $i < $this->size; $i++) {
                $v = (int)$res_val%2;
                if($res_val > 1) {
                    $res_val = (int)$res_val/2;
                } else {
                    $res_val = 0;
                }
                $x = $this->width - $this->border_width - (($i-16) * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->height - 2 * $this->border_height - $this->bit_height - 3
                    . "\"  font-size=\"10\">" . $v . "</text>\n");
            }
            for($i = 16; $i < $this->size; $i++) {
                $v = (int)$res_val%2;
                if($res_val > 1) {
                    $res_val = (int)$res_val/2;
                } else {
                    $res_val = 0;
                }
                $x = $this->width - $this->border_width - (($i-16) * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->height - 3 * $this->border_height - 2 * $this->bit_height - 3
                    . "\"  font-size=\"10\">" . $v . "</text>\n");
            }
            for($i = 16; $i < $this->size; $i++) {
                $v = (int)$res_val%2;
                if($res_val > 1) {
                    $res_val = (int)$res_val/2;
                } else {
                    $res_val = 0;
                }
                $x = $this->width - $this->border_width - (($i-16) * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->height - 4 * $this->border_height - 3 * $this->bit_height - 3
                    . "\"  font-size=\"10\">" . $v . "</text>\n");
            }
        } else if($this->size > 16) {
            for($i = 0; $i < 16; $i++) {
                $v = (int)$res_val%2;
                if($res_val > 1) {
                    $res_val = (int)$res_val/2;
                } else {
                    $res_val = 0;
                }
                $x = $this->width - $this->border_width - ($i * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->height - ($this->border_height) - 3
                    . "\"  font-size=\"10\">" . $v . "</text>\n");
            }
            for($i = 16; $i < $this->size; $i++) {
                $v = (int)$res_val%2;
                if($res_val > 1) {
                    $res_val = (int)$res_val/2;
                } else {
                    $res_val = 0;
                }
                $x = $this->width - $this->border_width - (($i-16) * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->height - 2 * $this->border_height - $this->bit_height - 3
                    . "\"  font-size=\"10\">" . $v . "</text>\n");
            }
        } else {
            for($i = 0; $i < $this->size; $i++) {
                $v = (int)$res_val%2;
                if($res_val > 1) {
                    $res_val = (int)$res_val/2;
                } else {
                    $res_val = 0;
                }
                $x = $this->width - $this->border_width - ($i * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->height - ($this->border_height) - 3
                    . "\"  font-size=\"10\">" . $v . "</text>\n");
            }
        }
    }

    function draw_bit_border($num, $name) {
        if($this->size > 32) {
            if(47 < $num) {
                $num = $num -48;
                echo("  <line id=\"border_" .  $name . "\" x1=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y1=\"" . $this->border_height
                    .                                  "\" x2=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y2=\"" . $this->border_height + $this->bit_height
                    . "\" stroke=\"black\" stroke-width=\"1\" />\n");
            }else if(31 < $num) {
                $num = $num -32;
                echo("  <line id=\"border_" .  $name . "\" x1=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y1=\"" . 2 * $this->border_height + $this->bit_height
                    .                                  "\" x2=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y2=\"" . 2 * $this->border_height + 2 * $this->bit_height
                    . "\" stroke=\"black\" stroke-width=\"1\" />\n");
            }else if(15 < $num) {
                $num = $num -16;
                echo("  <line id=\"border_" .  $name . "\" x1=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y1=\"" . 3 * $this->border_height + 2 * $this->bit_height
                    .                                  "\" x2=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y2=\"" . 3 * $this->border_height + 3 * $this->bit_height
                    . "\" stroke=\"black\" stroke-width=\"1\" />\n");
            } else {
                echo("  <line id=\"border_" .  $name . "\" x1=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y1=\"" . 4 * $this->border_height + 3 * $this->bit_height
                                                     . "\" x2=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y2=\"" . 4 * $this->border_height + 4 * $this->bit_height
                    . "\" stroke=\"black\" stroke-width=\"1\" />\n");
            }
        } else if($this->size > 16) {
            if(15 < $num) {
                $num = $num -16;
                echo("  <line id=\"border_" .  $name . "\" x1=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y1=\"" . $this->border_height
                    .                                  "\" x2=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y2=\"" . $this->border_height + $this->bit_height
                    . "\" stroke=\"black\" stroke-width=\"1\" />\n");
            } else {
                echo("  <line id=\"border_" .  $name . "\" x1=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y1=\"" . 2 * $this->border_height + $this->bit_height
                                                     . "\" x2=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y2=\"" . 2 * $this->border_height + 2 * $this->bit_height
                    . "\" stroke=\"black\" stroke-width=\"1\" />\n");
            }
        } else {
            echo("  <line id=\"border_" .  $name . "\" x1=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y1=\"" . $this->border_height
                                                 . "\" x2=\"" . $this->width - ($num * $this->bit_width) - $this->border_width . "\" y2=\"" . $this->height - ($this->border_height)
                . "\" stroke=\"black\" stroke-width=\"1\" />\n");
        }
    }


    function add_fields($arr_arr) {
        // Fields
        $borders_drawn = array();
        for($i = 0; $i< 128 + 1; $i++) {
            $borders_drawn[$i] = False;
        }
        $borders_drawn[0]  = True;
        $borders_drawn[15] = True;
        $borders_drawn[31] = True;
        $borders_drawn[47] = True;
        $borders_drawn[63] = True;

        $i = 0;
        foreach ($arr_arr as $row) {
            $bit_pos = ($row["bit_offset"] + $row["size_bit"]);
            // calculate y position for label
            if($this->size > 32) {
                if(47 < $bit_pos) {
                    $y = $this->border_height + ($this->bit_height - $this->res_val_hight)/2;
                } else if(31 < $bit_pos) {
                    $y = 2 * $this->border_height + $this->bit_height + ($this->bit_height - $this->res_val_hight)/2;
                } else if(15 < $bit_pos) {
                    $y = 3 * $this->border_height + 2 * $this->bit_height + ($this->bit_height - $this->res_val_hight)/2;
                } else {
                    $y = 4 * $this->border_height + 3 * $this->bit_height + ($this->bit_height - $this->res_val_hight)/2;
                }
            } else if($this->size > 16) {
                if(16 < $bit_pos) {
                    $y = $this->border_height + ($this->bit_height - $this->res_val_hight)/2;
                } else {
                    $y = 2 * $this->border_height + $this->bit_height + ($this->bit_height - $this->res_val_hight)/2;
                }
            } else {
                $y = $this->border_height + ($this->bit_height - $this->res_val_hight)/2;
            }

            // to avoid collisions alternating text a bit higher and a bit lower
            if(0 == $i%2) {
                $y = $y + 10;
            } else {
                $y = $y -2 ;
            }
            $i = $i + 1;

            // Name of field
            $labelPos = 0;
            if(47 < $bit_pos) {
                $labelPos = $bit_pos -48;
            } else if(31 < $bit_pos) {
                $labelPos = $bit_pos -32;
            } else if(15 < $bit_pos) {
                $labelPos = $bit_pos -16;
            } else {
                $labelPos = $bit_pos;
            }
            $name_start = $this->width - ($labelPos * $this->bit_width) - $this->border_width;

            echo("  <text id=\"field_" . $row["name"] . "\" x=\"" . $name_start + 3
                . "\" y=\"" . $y . "\"  font-size=\"10\">" . $row["name"] . "</text>\n");

            // draw Borders
            if(False == $borders_drawn[$bit_pos]) {
                $this->draw_bit_border($bit_pos, $row["name"]);
                $borders_drawn[$bit_pos] = True;
            }
            $num = $row["bit_offset"];
            if(False == $borders_drawn[$num]) {
                $this->draw_bit_border($num, $row["name"]);
                $borders_drawn[$num] = True;
            }

        }
    }

    function create_image($arr_arr, $name, $size, $access, $res_val) {
        if(1>$size) {
            // size is invalid -> default is 64 bit
            $size = 32;
        }
        $this->size = $size;
        $this->rows = $size / 16;
        $bitPerRow = 16;
        if($size < 9) {
            $bitPerRow = 8;
        }
        $this->width = ($bitPerRow * $this->bit_width) + (2 * $this->border_width);
        $this->height = ($this->bit_height +  $this->border_height) * $this->rows + $this->border_height;

        echo("<svg width=\"" . $this->width . "\" height=\"" . $this->height . "\" viewbox=\"0 0 " . $this->width . " " . $this->height . "\">\n");
        echo("  <title>" . $name . "</title>\n");

        $this->add_reg_name($name);
        $this->add_reg_access_mode($access);
        $this->add_box();
        $this->add_bit_labels();
        $this->add_reset_value($res_val);
        $this->add_fields($arr_arr);

        // end of SVG
        echo("</svg>\n");
    }

}

function register_image($arr_arr, $name, $size, $access, $res_val) {
    $img = new svg();
    $img->create_image($arr_arr, $name, $size, $access, $res_val);
}

?>