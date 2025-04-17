<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\learning\notes\infrastructure\persistence;

use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\modules\learning\notes\core\entities\Note;
use bpmj\wpidea\modules\learning\notes\core\value_objects\{Lesson_ID, Module_ID, Note_ID, User_ID};

class Note_Persistence implements Interface_Note_Persistence
{
    public const TABLE_NAME = 'wpi_notes';

    private Interface_Database $db;

    public function __construct(Interface_Database $db)
    {
        $this->db = $db;
    }

    public function insert(Note $note): void
    {
        $this->db->insert(self::TABLE_NAME, [
            'user_id' => $note->get_user_id()->to_int(),
            'lesson_id' => $note->get_lesson_id() ? $note->get_lesson_id()->to_int() : null,
            'module_id' => $note->get_module_id() ? $note->get_module_id()->to_int() : null,
            'contents' => strip_tags($note->get_content())
        ]);
    }

    public function update(Note $note): void
    {
        $set = [
            ['user_id', $note->get_user_id()->to_int()],
            ['lesson_id', $note->get_lesson_id() ? $note->get_lesson_id()->to_int() : null],
            ['module_id', $note->get_module_id() ? $note->get_module_id()->to_int() : null],
            ['contents', strip_tags($note->get_content(), "\n")]
        ];

        $where = [
            ['id', '=', $note->get_id()->to_int()]
        ];

        $this->db->update_rows(self::TABLE_NAME, $set, $where);
    }

    public function find_by_id(Note_ID $id): array
    {
        $where = [
            ['id', '=', $id->to_int()]
        ];

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'user_id',
            'lesson_id',
            'module_id',
            'contents',
        ], $where, 1);
    }

    public function find_by_user_and_lesson_id(User_ID $user_id, Lesson_ID $lesson_id): array
    {
        $where = [
            ['user_id', '=', $user_id->to_int()],
            ['lesson_id', '=', $lesson_id->to_int()]
        ];

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'user_id',
            'lesson_id',
            'module_id',
            'contents',
        ], $where, 1);
    }

    public function find_by_user_and_module_id(User_ID $user_id, Module_ID $module_id): array
    {
        $where = [
            ['user_id', '=', $user_id->to_int()],
            ['module_id', '=', $module_id->to_int()]
        ];

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'user_id',
            'lesson_id',
            'module_id',
            'contents',
        ], $where, 1);
    }

    public function delete(Note_ID $id): void
    {
        $where = [
            ['id', '=', $id->to_int()]
        ];

        $this->db->delete_rows(self::TABLE_NAME, $where);
    }

    public function setup(): void
    {
        $this->db->create_table_if_not_exists(self::TABLE_NAME, [
            'id bigint(20) NOT NULL AUTO_INCREMENT',
            'user_id bigint(20) NOT NULL',
            'lesson_id bigint(20) NULL',
            'module_id bigint(20) NULL',
            'contents text NULL',
        ], 'id');
    }
}