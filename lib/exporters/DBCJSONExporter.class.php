<?php
/**
 * World of Warcraft DBC Library
 * Copyright (c) 2011 Tim Kurvers <http://www.moonsphere.net>
 * This library allows creation, reading and export of World of Warcraft's
 * client-side database files. These so-called DBCs store information
 * required by the client to operate successfully and can be extracted
 * from the MPQ archives of the actual game client.
 * The contents of this file are subject to the MIT License, under which
 * this library is licensed. See the LICENSE.md file for the full license.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category Exporters
 * @package  PhpDbc
 * @author   Tim Kurvers <tim@moonsphere.net>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://github.com/timkurvers/wow-dbc
 */

/**
 * JSON Exporter
 *
 * @category Exporters
 * @package  PhpDbc
 * @author   Tim Kurvers <tim@moonsphere.net>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://github.com/timkurvers/wow-dbc
 */
class DBCJSONExporter implements IDBCExporter
{

    /**
     * Exports given DBC in JSON format to given target (defaults to output stream)
     *
     * @param DBC    $dbc
     * @param string $target
     *
     * @return
     */
    public function export(DBC $dbc, $target = self::OUTPUT)
    {
        $map = $dbc->getMap();
        if ($map === null) {
            throw new DBCException(self::NO_MAP);
            return;
        }

        $data = array(
            'fields' => array(),
            'records' => array()
        );

        $fields = $map->getFields();
        foreach ($fields as $name => $rule) {
            $count = max($rule & 0xFF, 1);
            if ($rule & DBCMap::UINT_MASK) {
                $type = 'uint';
            } else if ($rule & DBCMap::INT_MASK) {
                $type = 'int';
            } else if ($rule & DBCMap::FLOAT_MASK) {
                $type = 'float';
            } else if ($rule & DBCMap::STRING_MASK || $rule & DBCMap::STRING_LOC_MASK) {
                $type = 'string';
            }
            for ($i = 1; $i <= $count; $i++) {
                $suffix = ($count > 1) ? $i : '';
                $data['fields'][$name . $suffix] = $type;
            }
        }
        foreach ($dbc as $record) {
            $data['records'][] = array_values($record->extract());
        }

        file_put_contents($target, $this->_json_indent(json_encode($data)));
    }

    /**
     * Indents a flat JSON string to make it more human-readable.
     *
     * @param string $json The original JSON string to process.
     *
     * @return string Indented version of the original JSON string.
     */
    private function _json_indent($json)
    {

        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = '  ';
        $newLine = "\n";
        $prevChar = '';
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else if (($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }
}
