<?php
namespace Codappix\CdxMigration\Tests\Fixtures\Migration\Model;

/*
 * Copyright (C) 2017  Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use Codappix\CdxMigration\Migration\Model\To;

class NewRecordType extends To
{
    public static $table_name = 'sys_categories';

    public function set_pid($value)
    {
        $this->pid = $value;
    }

    public function get_pid()
    {
        return $this->pid;
    }

    public function set_hidden($value)
    {
        $this->hidden = $value;
    }

    public function get_hidden()
    {
        return $this->hidden;
    }

    public function set_tstampField($value)
    {
        $this->tstampField = $value;
    }

    public function get_tstampField()
    {
        return $this->tstampField;
    }

    public function set_crdateField($value)
    {
        $this->crdateField = $value;
    }

    public function get_crdateField()
    {
        return $this->crdateField;
    }

    public function set_cruserIdField($value)
    {
        $this->cruserIdField = $value;
    }

    public function get_cruserIdField()
    {
        return $this->cruserIdField;
    }
}
