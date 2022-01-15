<?php

class svg {

    public $border_width = 100;
    public $border_height = 60;
    public $bit_width = 60;
    public $bit_height = 50;
    public $tik_len = 5;
    public $res_val_hight = 15;
    public $width = 0;
    public $height = 0;
    public $size = 0;

    function add_reg_name($name) {
        // name of register
        echo("  <text x=\"" . $this->border_width/2  . "\" y=\"10\" font-size=\"10\">" . $name . "</text>\n");
    }

    function add_reg_access_mode($access) {
        // access mode (read only / write only / read-write
        echo("  <text x=\"" . $this->width - 30 - $this->border_width/2 . "\" y=\"10\" font-size=\"10\">" . $access . "</text>\n");
    }

    function add_box() {
        if($this->size > 16) {
            // top box
            // top line
            echo("  <line id=\"top\" x1=\"" . $this->border_width/2 . "\"  y1=\"" . $this->border_height/2
                . "\" x2=\"" . $this->width - $this->border_width/2 . "\" y2=\"" . $this->border_height/2
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");
            // bottom line
            echo("  <line id=\"bottom\" x1=\"" . $this->border_width/2 . "\"  y1=\"" . ($this->height/2) - ($this->border_height/2)
                . "\" x2=\"" . $this->width - $this->border_width/2 . "\" y2=\"" . ($this->height/2) - ($this->border_height/2)
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");
            // right line
            echo("  <line id=\"right\" x1=\"" . $this->width - $this->border_width/2 . "\"  y1=\"" . $this->border_height/2
                . "\" x2=\"" . $this->width - $this->border_width/2 . "\" y2=\"" . ($this->height/2) - ($this->border_height/2)
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");
            // left line
            echo("  <line id=\"left\" x1=\"" . $this->border_width/2 . "\"  y1=\"" . $this->border_height/2
                . "\" x2=\"" . $this->border_width/2 . "\" y2=\"" . ($this->height/2) - ($this->border_height/2)
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");

            // bottom box
            // top line
            echo("  <line id=\"top\" x1=\"" . $this->border_width/2 . "\"  y1=\"" . $this->border_height/2 + ($this->height/2)
                . "\" x2=\"" . $this->width - $this->border_width/2 . "\" y2=\"" . $this->border_height/2 + ($this->height/2)
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");
            // bottom line
            echo("  <line id=\"bottom\" x1=\"" . $this->border_width/2 . "\"  y1=\"" . $this->height - ($this->border_height/2)
                . "\" x2=\"" . $this->width - $this->border_width/2 . "\" y2=\"" . $this->height - ($this->border_height/2)
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");
            // right line
            echo("  <line id=\"right\" x1=\"" . $this->width - $this->border_width/2 . "\"  y1=\"" . $this->border_height/2 + ($this->height/2)
                . "\" x2=\"" . $this->width - $this->border_width/2 . "\" y2=\"" . $this->height - ($this->border_height/2)
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");
            // left line
            echo("  <line id=\"left\" x1=\"" . $this->border_width/2 . "\"  y1=\"" . $this->border_height/2 + ($this->height/2)
                . "\" x2=\"" . $this->border_width/2 . "\" y2=\"" . $this->height - ($this->border_height/2)
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");

        } else {
            // top line
            echo("  <line id=\"top\" x1=\"" . $this->border_width/2 . "\"  y1=\"" . $this->border_height/2
                . "\" x2=\"" . $this->width - $this->border_width/2 . "\" y2=\"" . $this->border_height/2
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");
            // bottom line
            echo("  <line id=\"bottom\" x1=\"" . $this->border_width/2 . "\"  y1=\"" . $this->height - ($this->border_height/2)
                . "\" x2=\"" . $this->width - $this->border_width/2 . "\" y2=\"" . $this->height - ($this->border_height/2)
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");
            // right line
            echo("  <line id=\"right\" x1=\"" . $this->width - $this->border_width/2 . "\"  y1=\"" . $this->border_height/2
                . "\" x2=\"" . $this->width - $this->border_width/2 . "\" y2=\"" . $this->height - ($this->border_height/2)
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");
            // left line
            echo("  <line id=\"left\" x1=\"" . $this->border_width/2 . "\"  y1=\"" . $this->border_height/2
                . "\" x2=\"" . $this->border_width/2 . "\" y2=\"" . $this->height - ($this->border_height/2)
                . "\" stroke=\"black\" stroke-width=\"2\" />\n");
        }
    }

    function add_bit_labels() {
        // bit tiks
        if($this->size > 16) {
            for($i = 0; $i < 17; $i++)
            {
                $x = $this->width - $this->border_width/2 - ($i * $this->bit_width);
                if(0 == $i%4) {
                    echo("  <line id=\"tick_" . $i . "\" x1=\"" . $x . "\"  y1=\"" . ($this->border_height/2) - ($this->tik_len * 2) + ($this->height/2)
                        . "\" x2=\"" . $x . "\" y2=\"" . ($this->border_height/2) + ($this->height/2)
                        . "\" stroke=\"black\" stroke-width=\"1\" />\n");
                } else {
                    echo("  <line id=\"tick" . $i . "\" x1=\"" . $x . "\"  y1=\"" . ($this->border_height/2) - $this->tik_len + ($this->height/2)
                        . "\" x2=\"" . $x . "\" y2=\"" . ($this->border_height/2) + ($this->height/2)
                        . "\" stroke=\"black\" stroke-width=\"1\" />\n");
                }
            }

            for($i = 16; $i < ($this->size + 1); $i++)
            {
                $x = $this->width - $this->border_width/2 - (($i - 16) * $this->bit_width);
                if(0 == $i%4) {
                    echo("  <line id=\"tick_" . $i . "\" x1=\"" . $x . "\"  y1=\"" . ($this->border_height/2) - $this->tik_len * 2
                        . "\" x2=\"" . $x . "\" y2=\"" . ($this->border_height/2)
                        . "\" stroke=\"black\" stroke-width=\"1\" />\n");
                } else {
                    echo("  <line id=\"tick" . $i . "\" x1=\"" . $x . "\"  y1=\"" . ($this->border_height/2) - $this->tik_len
                        . "\" x2=\"" . $x . "\" y2=\"" . ($this->border_height/2)
                        . "\" stroke=\"black\" stroke-width=\"1\" />\n");
                }
            }
        } else {
            for($i = 0; $i < ($this->size + 1); $i++)
            {
                $x = $this->width - $this->border_width/2 - ($i * $this->bit_width);
                if(0 == $i%4) {
                    echo("  <line id=\"tick_" . $i . "\" x1=\"" . $x . "\"  y1=\"" . ($this->border_height/2) - $this->tik_len * 2
                        . "\" x2=\"" . $x . "\" y2=\"" . ($this->border_height/2)
                        . "\" stroke=\"black\" stroke-width=\"1\" />\n");
                } else {
                    echo("  <line id=\"tick" . $i . "\" x1=\"" . $x . "\"  y1=\"" . ($this->border_height/2) - $this->tik_len
                        . "\" x2=\"" . $x . "\" y2=\"" . ($this->border_height/2)
                        . "\" stroke=\"black\" stroke-width=\"1\" />\n");
                }
            }
        }

        // bit nums
        if($this->size > 16) {
            for($i = 0; $i < 16; $i++)
            {
                $x = $this->width - $this->border_width/2 - ($i * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->border_height/2 - 3 + ($this->height/2)
                    . "\"  font-size=\"10\">" . $i . "</text>\n");
            }
            for($i = 16; $i < $this->size; $i++)
            {
                $x = $this->width - $this->border_width/2 - (($i - 16) * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->border_height/2 - 3
                    . "\"  font-size=\"10\">" . $i . "</text>\n");
            }
        } else {
            for($i = 0; $i < $this->size; $i++)
            {
                $x = $this->width - $this->border_width/2 - ($i * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->border_height/2 - 3
                    . "\"  font-size=\"10\">" . $i . "</text>\n");
            }
        }
    }

    function add_reset_value($res_val) {
        // Reset Value
        // dividing Line
        echo("  <line id=\"res_val\" x1=\"" . $this->border_width/2 . "\"  y1=\"" . $this->height - ($this->border_height/2) - $this->res_val_hight
            . "\" x2=\"" . $this->width - $this->border_width/2 . "\" y2=\"" . $this->height - ($this->border_height/2) - $this->res_val_hight
            . "\" stroke=\"black\" stroke-width=\"1\" />\n");
        if($this->size > 16) {
            // we need two lines
            echo("  <line id=\"res_val\" x1=\"" . $this->border_width/2 . "\"  y1=\"" . ($this->height/2) - ($this->border_height/2) - $this->res_val_hight
                . "\" x2=\"" . $this->width - $this->border_width/2 . "\" y2=\"" . ($this->height/2) - ($this->border_height/2) - $this->res_val_hight
                . "\" stroke=\"black\" stroke-width=\"1\" />\n");
        }
        // Label for Reset Value
        echo("  <text x=\"0\" y=\"" . $this->height - ($this->border_height/2) - 3 . "\" font-size=\"10\">Resets to</text>\n");
        if($this->size > 16) {
            // we need two labels
            echo("  <text x=\"0\" y=\"" . ($this->height/2) - ($this->border_height/2) - 3 . "\" font-size=\"10\">Resets to</text>\n");
        }

        // Reset Value Numbers
        if($this->size > 16) {
            for($i = 0; $i < 16; $i++)
            {
                $v = (int)$res_val%2;
                if($res_val > 1)
                {
                    $res_val = (int)$res_val/2;
                }
                else
                {
                    $res_val = 0;
                }
                $x = $this->width - $this->border_width/2 - ($i * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->height - ($this->border_height/2) - 3
                    . "\"  font-size=\"10\">" . $v . "</text>\n");
            }
            for($i = 16; $i < $this->size; $i++)
            {
                $v = (int)$res_val%2;
                if($res_val > 1)
                {
                    $res_val = (int)$res_val/2;
                }
                else
                {
                    $res_val = 0;
                }
                $x = $this->width - $this->border_width/2 - (($i-16) * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . ($this->height/2) - ($this->border_height/2) - 3
                    . "\"  font-size=\"10\">" . $v . "</text>\n");
            }
        } else {
            for($i = 0; $i < $this->size; $i++)
            {
                $v = (int)$res_val%2;
                if($res_val > 1)
                {
                    $res_val = (int)$res_val/2;
                }
                else
                {
                    $res_val = 0;
                }
                $x = $this->width - $this->border_width/2 - ($i * $this->bit_width) - $this->bit_width/2;
                echo("  <text id=\"bit_" . $i . "\" x=\"" . $x . "\" y=\"" . $this->height - ($this->border_height/2) - 3
                    . "\"  font-size=\"10\">" . $v . "</text>\n");
            }
        }
    }

    function draw_bit_border($num, $name) {
        if($this->size > 16) {
            if(15 < $num) {
                $num = $num -16;
                echo("  <line id=\"border_" .  $name . "\" x1=\"" . $this->width - ($num * $this->bit_width) - $this->border_width/2 . "\"  y1=\"" . $this->border_height/2
                    . "\" x2=\"" . $this->width -($num * $this->bit_width) - $this->border_width/2 . "\" y2=\"" . ($this->height/2) - ($this->border_height/2)
                    . "\" stroke=\"black\" stroke-width=\"1\" />\n");
            } else {
                echo("  <line id=\"border_" .  $name . "\" x1=\"" . $this->width - ($num * $this->bit_width) - $this->border_width/2 . "\"  y1=\"" . $this->border_height/2 + ($this->height/2)
                    . "\" x2=\"" . $this->width -($num * $this->bit_width) - $this->border_width/2 . "\" y2=\"" . $this->height - ($this->border_height/2)
                    . "\" stroke=\"black\" stroke-width=\"1\" />\n");
            }
        } else {
            echo("  <line id=\"border_" .  $name . "\" x1=\"" . $this->width - ($num * $this->bit_width) - $this->border_width/2 . "\"  y1=\"" . $this->border_height/2
                . "\" x2=\"" . $this->width -($num * $this->bit_width) - $this->border_width/2 . "\" y2=\"" . $this->height - ($this->border_height/2)
                . "\" stroke=\"black\" stroke-width=\"1\" />\n");
        }
    }


    function add_fields($arr_arr) {
        // Fields
        $i = 0;
        $borders_drawn = array();
        for($i = 0; $i< $this->size + 1; $i++) {
            $borders_drawn[$i] = False;
        }
        foreach ($arr_arr as $row) {
            $bit_pos = ($row["bit_offset"] + $row["size_bit"]);
            if($this->size > 16) {
                if(16 < $bit_pos) {
                    if(0 == $i%2) {
                        $y = $this->border_height/2 + ($this->bit_height - $this->res_val_hight)/2 + 10;
                    } else {
                        $y = $this->border_height/2 + ($this->bit_height - $this->res_val_hight)/2 - 2;
                    }
                } else {
                    if(0 == $i%2) {
                        $y = ($this->height/2) + $this->border_height/2 + ($this->bit_height - $this->res_val_hight)/2 + 10;
                    } else {
                        $y = ($this->height/2) + $this->border_height/2 + ($this->bit_height - $this->res_val_hight)/2 - 2;
                    }
                }
            } else {
                if(0 == $i%2) {
                    $y = $this->border_height/2 + ($this->bit_height - $this->res_val_hight)/2 + 10;
                } else {
                    $y = $this->border_height/2 + ($this->bit_height - $this->res_val_hight)/2 - 2;
                }
            }
            $i = $i + 1;
            // Name of field
            if(16 < $bit_pos) {
                $name_start = $this->width - (($bit_pos -16) * $this->bit_width) - $this->border_width/2;
            } else {
                $name_start = $this->width - ($bit_pos * $this->bit_width) - $this->border_width/2;
            }
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
            // size is invalid -> default is 32 bit
            $size = 32;
        }
        $this->size = $size;
        if(16 < $size) {
            $this->width = (($size/2) * $this->bit_width) + $this->border_width;
            $this->height = $this->bit_height * 2 + $this->border_height * 2;
        } else {
            $this->width = ($size * $this->bit_width) + $this->border_width;
            $this->height = $this->bit_height + $this->border_height;
        }

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