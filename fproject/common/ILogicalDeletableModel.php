<?php
///////////////////////////////////////////////////////////////////////////////
//
// © Copyright f-project.net 2010-present.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
///////////////////////////////////////////////////////////////////////////////

namespace fproject\common;

interface ILogicalDeletableModel
{
    /**
     * Get the query criteria to determine the model is not logically deleted
     * @return array the query criteria. See the following examples:
     * Example 1:
     * ```php
     *      ['status=1']
     * ```
     * Example 2:
     * ```php
     *      ['condition'=>'status=1']
     * ```
     * Example 3:
     * ```php
     *      ['condition'=>'status=:deleted', 'param'=> [':deleted' => 1]]
     * ```
     */
    public static function getIsNotDeletedCriteria();

    /**
     * Determine if the current model is logically deletable
     * @return bool TRUE if the current model is logically deletable
     */
    public function isLogicalDeletable();

    /**
     * Delete the model logically
     * @param mixed $pk primary key value(s). Use array for multiple primary keys. For composite key, each key value must be an array (column name=>column value).
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer the number of rows deleted
     */
    public function logicalDeleteByPk($pk, $condition='',$params=[]);
}