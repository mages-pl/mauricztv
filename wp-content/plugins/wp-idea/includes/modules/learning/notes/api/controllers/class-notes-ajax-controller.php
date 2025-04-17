<?php

namespace bpmj\wpidea\modules\learning\notes\api\controllers;

use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\learning\notes\core\exceptions\Invalid_Courses_Content_Id_Exception;
use bpmj\wpidea\modules\learning\notes\core\exceptions\Note_Not_Found_Exception;
use bpmj\wpidea\modules\learning\notes\core\services\Interface_Note_Service;
use bpmj\wpidea\modules\learning\notes\core\value_objects\{Lesson_ID, Module_ID, Note_ID, User_ID};
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use Error;
use Exception;
use OutOfBoundsException;

class Notes_Ajax_Controller extends Ajax_Controller
{
    private Interface_Note_Service $note_service;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Note_Service $note_service,
        Interface_Current_User_Getter $current_user_getter
    ) {
        $this->note_service = $note_service;
        $this->current_user_getter = $current_user_getter;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => array_merge(Caps::ROLES_ADMINS_SUPPORT_SUBSCRIBER, [Caps::ROLE_LMS_PARTNER]),
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function get_action(Current_Request $current_request): string
    {
        $id_lesson = $current_request->get_body_arg('id_lesson');
        $id_module = $current_request->get_body_arg('id_module');

        $current_user_id = $this->get_current_user_id();

        try {
            $id_lesson = $id_lesson ? new Lesson_ID($id_lesson) : null;
            $id_module = $id_module ? new Module_ID($id_module) : null;

            $note = $this->note_service->find_by_user_and_courses_content_id(new User_ID($current_user_id), $id_lesson, $id_module);
        } catch (Invalid_Courses_Content_Id_Exception | OutOfBoundsException $e) {
            return $this->fail($this->translator->translate('notes.actions.get.error'));
        }

        if ($note && !$this->note_belongs_to_user($note->get_id())) {
            return $this->fail($this->translator->translate('notes.actions.get.error'));
        }

        return $this->success(
            [
                'note_id' => $note ? $note->get_id()->to_int() : null,
                'note_content' => $note ? $note->get_content() : null
            ]
        );
    }

    public function save_action(Current_Request $current_request): string
    {
        
        $id = $current_request->get_body_arg('id');
        $contents = str_replace(array("\r\n", "", "&amp;", "&nbsp;", "\r", "\n"), '', strip_tags(($current_request->get_body_arg('contents'))));
        $id_lesson = $current_request->get_body_arg('id_lesson');
        $id_module = $current_request->get_body_arg('id_module');

        $id = $id ? new Note_ID($id) : null;
        $id_lesson = $id_lesson ? new Lesson_ID($id_lesson) : null;
        $id_module = $id_module ? new Module_ID($id_module) : null;

        $current_user_id = $this->get_current_user_id();

        if (!$current_user_id) {
            return $this->fail($this->translator->translate('notes.actions.add.error'));
        }

        if (!$id) {
            return $this->add_note(new User_ID($current_user_id), $id_lesson, $id_module, $contents);
        }

        if (!$this->note_belongs_to_user($id)) {
            return $this->fail($this->translator->translate('notes.actions.add.error'));
        }

        return $this->update_note($id, $contents);
    }

    public function delete_action(Current_Request $current_request): string
    {
        $id = $current_request->get_body_arg('id');

        $id = $id ? new Note_ID($id) : null;

        if (!$this->note_belongs_to_user($id)) {
            return $this->fail($this->translator->translate('notes.actions.delete.error'));
        }

        $this->note_service->delete($id);

        return $this->success(
            [
                'message' => $this->translator->translate('notes.actions.delete.success')
            ]
        );
    }

    private function add_note(User_ID $current_user_id, ?Lesson_ID $id_lesson, ?Module_ID $id_module, string $contents): string
    {
        try {
            $this->note_service->add($current_user_id, $id_lesson, $id_module, $contents);
        } catch (Invalid_Courses_Content_Id_Exception | OutOfBoundsException $e) {
            return $this->fail($this->translator->translate('notes.actions.add.error'));
        }

        return $this->success(
            [
                'message' => $this->translator->translate('notes.actions.add.success')
            ]
        );
    }

    private function update_note(Note_ID $id, string $contents): string
    {
        try {
            $this->note_service->update($id, $contents);
        } catch (Note_Not_Found_Exception $e) {
            return $this->fail($this->translator->translate('notes.actions.save.error'));
        }

        return $this->success(
            [
                'message' => $this->translator->translate('notes.actions.save.success')
            ]
        );
    }

    private function note_belongs_to_user(Note_ID $note_id): bool
    {
        try {
            $note_user_id = $this->note_service->find_by_id($note_id)->get_user_id()->to_int();
            $current_user_id = $this->current_user_getter->get()->get_id()->to_int();
        } catch (Exception | Error $e) {
            return false;
        }

        return $note_user_id === $current_user_id;
    }

    private function get_current_user_id(): ?int
    {
        $user = $this->current_user_getter->get();

        if (!$user) {
            return null;
        }

        return $user->get_id()->to_int();
    }

    private function fix_new_lines(string $text): string
    {
        return nl2br(htmlentities($text, ENT_QUOTES, 'UTF-8'));
    }
}

