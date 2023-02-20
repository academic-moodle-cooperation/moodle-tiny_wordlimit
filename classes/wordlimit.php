<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace tiny_wordlimit;

use assign_submission_onlinetext;
use qtype_essay_question;

/**
 * Tiny Wordlimit plugin class for detecting if wordlimits are present.
 *
 * @package    tiny_wordlimit
 * @copyright  2023 University of Graz
 * @author     André Menrath <andre.menrath@uni-graz.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wordlimit {
    /**
     * Returns the wordlimits found on the current page.
     * Within an quiz it returns the wordlimits for all question where the index is the
     * question slot. Within an assignment the wordlimit is returned at index 1.
     *
     * @param context $context The context that the editor is used within
     * @return array
     */
    public static function detect_wordlimits_on_page($context) {
        global $attemptobj, $assign, $slots;
        // TODO: use context and page path as well to make this more bullet proof.
        unset($context);
        $wordlimits = [];
        // Get wordlimits for a quiz.
        if ($attemptobj && $slots) {
            // We don't have access to which question the editor is bound to, so we get the wordlimits for all slots on this page.
            foreach ($slots as $slot) {
                $question = $attemptobj->get_question_attempt($slot)->get_question(false);
                if ($question instanceof qtype_essay_question) {
                    $wordlimits[$slot] = $question->maxwordlimit;
                }
            }
        }
        // Get wordlimit for an online submission within an assignment.
        if ($assign) {
            $assignment = $assign->get_submission_plugin_by_type('onlinetext');
            if ($assignment instanceof assign_submission_onlinetext && $assignment->get_config('wordlimitenabled')) {
                $wordlimit = $assignment->get_config('wordlimit');
                $wordlimits[1] = $wordlimit;
            }
        }
        return $wordlimits;
    }
}
