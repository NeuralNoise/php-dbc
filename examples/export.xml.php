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
 * @category Examples
 * @package  PhpDbc
 * @author   Tim Kurvers <tim@moonsphere.net>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://github.com/timkurvers/wow-dbc
 */

error_reporting(E_ALL | E_STRICT);
ini_set('memory_limit', -1);

// When exporting to the standard PHP output, ensure the browser expects a JSON-document
if (!defined('STDIN'))
    header('Content-Type: application/xml');

require '../lib/bootstrap.php';

/**
 * This example shows how to export a DBC-file to JSON format
 */

// Open given DBC and given map (ensure read-access on both)
// $dbc = new DBC('./dbcs/Sample.dbc', DBCMap::fromINI('./maps/Sample.ini'));

$files = scandir('./dbcs/');

foreach ($files as $key => $dbcName) {
    if ($dbcName != '.' && $dbcName != '..' && $dbcName != 'Sample.dbc') {
        if (file_exists('./maps/' . strstr($dbcName, '.dbc', true) . '.ini')) {
            echo 'Parsing ';

            // Open the given DBC (ensure read-access)
            $dbc = new DBC('./dbcs/' . $dbcName, DBCMap::fromINI('./maps/' . strstr($dbcName, '.dbc', true) . '.ini'));

            // Set up a new JSON exporter
            $json = new DBCXMLExporter();

            // Alternatively supports exporting to a file by providing a second argument
            $json->export($dbc, './export/' . strstr($dbcName, '.dbc', true) . '.json');
        } else {
            echo 'Skipping ';
        }

        echo './dbcs/' . $dbcName . PHP_EOL;
    }
}
