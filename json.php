<?php
/**
 * Enable json pretty print
 *
 * @var boolean
 */
private $printPrint = false;

/**
 * Set pretty print
 *
 * @param boolean $prettyPrint
 */
public setPrettyPrint($prettyPrint)
{
    $this->prettyPrint = $prettyPrint;
}

/**
 * Get pretty print
 *
 * @return $prettyPrint
 */
public getPrettyPrint()
{
    return $this->prettyPrint;
}

/**
 * Get human-readable json from a one-liner json string
 *
 * @return string human-readable json
 */
public function __toJson()
{
    if (! $this->prettyPrint || $this->prettyPrint && ! defined(JSON_PRETTY_PRINT)) {
        if (defined(JSON_NUMERIC_CHECK) && $this->jsonNumericCheck) {
            $json = json_encode($this->toArray(), JSON_NUMERIC_CHECK);
        } else {
            $json = json_encode($this->toArray());
        }
        if (! $this->prettyPrint) {
            return $json;
        }
    }

    // pretty print if requested
    //json pretty printing is supported in php >=5.4
    if (defined(JSON_PRETTY_PRINT)) {
        if (defined(JSON_NUMERIC_CHECK) && $this->jsonNumericCheck) {
            return json_encode($this->toArray(), JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        }
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
    $tab = '  ';
    $newJson = '';
    $indentLevel = 0;
    $inString = false;

    $len = strlen($json);
    for ($c = 0; $c < $len; $c++) {
        $char = $json[$c];
        switch($char) {
            case '{':
            case '[':
                if (! $inString) {
                    $newJson .= $char . "\n" . str_repeat($tab, $indentLevel + 1);
                    $indentLevel++;
                } else {
                    $newJson .= $char;
                }
                break;
            case '}':
            case ']':
                if (! $inString) {
                    $indentLevel--;
                    $newJson .= "\n" . str_repeat($tab, $indentLevel) . $char;
                } else {
                    $newJson .= $char;
                }
                break;
            case ',':
                if (! $inString) {
                    $newJson .= ",\n" . str_repeat($tab, $indentLevel);
                } else {
                    $newJson .= $char;
                }
                break;
            case ':':
                if (! $inString) {
                    $newJson .= ": ";
                } else {
                    $newJson .= $char;
                }
                break;
            case '"':
                if ($c > 0 && $json[$c - 1] != '\\') {
                    $inString = ! $inString;
                }
            default:
                $newJson .= $char;
                break;
        }
    }
    return $newJson;
}
?>