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

error_reporting(E_ALL);

require '../lib/bootstrap.php';

/**
 * This example shows how to generate a DBC mapping by sampling a given DBC
 */

$files = scandir('./dbcs/');

foreach ($files as $key => $dbcName) {
    if ($dbcName != '.' && $dbcName != '..' && $dbcName != 'Sample.dbc') {
        echo './dbcs/' . $dbcName . PHP_EOL;

        // Open the given DBC (ensure read-access)
        $dbc = new DBC('./dbcs/' . $dbcName);

        // Construct a map by predicting the fields in the given DBC
        $map = DBCMap::fromDBC($dbc);

        // Dump the first record using the mappings
        echo $dbc->getRecordCount() . ' records.' . PHP_EOL;
        // $dbc->getRecord(0)->dump(true);

        // var_dump($map);

        $map->toINI('./maps/' . strstr($dbcName, '.dbc', true) . '.ini');
    }
}
