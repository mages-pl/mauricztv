<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\learning\notes\infrastructure\repositories;

use bpmj\wpidea\modules\learning\notes\core\entities\Note;
use bpmj\wpidea\modules\learning\notes\core\repositories\Interface_Note_Repository;
use bpmj\wpidea\modules\learning\notes\core\value_objects\{Lesson_ID, Module_ID, Note_ID, User_ID};
use bpmj\wpidea\modules\learning\notes\infrastructure\persistence\Interface_Note_Persistence;

class Note_Repository implements Interface_Note_Repository
{
    private Interface_Note_Persistence $note_persistence;

    public function __construct(Interface_Note_Persistence $note_persistence)
    {
        $this->note_persistence = $note_persistence;
    }

    public function find_by_id(Note_ID $id): ?Note
    {
        $note_rows = $this->note_persistence->find_by_id($id);

        if (empty($note_rows)) {
            return null;
        }

        return $this->table_row_to_note_model($note_rows[0]);
    }

    public function find_by_user_and_lesson_id(User_ID $user_id, Lesson_ID $lesson_id): ?Note
    {
        $note_rows = $this->note_persistence->find_by_user_and_lesson_id($user_id, $lesson_id);

        if (empty($note_rows)) {
            return null;
        }

        return $this->table_row_to_note_model($note_rows[0]);
    }

    public function find_by_user_and_module_id(User_ID $user_id, Module_ID $module_id): ?Note
    {
        $note_rows = $this->note_persistence->find_by_user_and_module_id($user_id, $module_id);

        if (empty($note_rows)) {
            return null;
        }

        return $this->table_row_to_note_model($note_rows[0]);
    }

    public function add(Note $note): void
    {
        $this->note_persistence->insert($note);
    }

    public function update(Note $note): void
    {
        $this->note_persistence->update($note);
    }

    public function delete(Note_Id $id): void
    {
        $this->note_persistence->delete($id);
    }

    private function table_row_to_note_model(array $row): Note
    {
        $id = $row['id'] ?? null;

        return new Note(
            $id ? new Note_ID((int)$id) : null,
            new User_ID((int)$row['user_id']),
            $row['lesson_id'] ? new Lesson_ID((int)$row['lesson_id']) : null,
            $row['module_id'] ? new Module_ID((int)$row['module_id']) : null,
            strip_tags($row['contents'])
        );
    }

}