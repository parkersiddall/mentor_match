<?php
    class Matcher {
        private $pdo;
        private $matching_params = array();
        private $match_form_id;
        private $open_mentors = array();
        private $open_mentees = array();
        private $ratio;

        function __construct($id, $db_conn) {
            $this->match_form_id = $id;
            $this->pdo = $db_conn;
        }
        public function match_open_mentees() {  // see docs for algorithm sudo code
            $this->set_ratio();
            $this->set_matching_params();
            $this->set_open_applications();
                // if 0 return error

            for ($i = count($this->matching_params); $i >= 0; $i--) {
                foreach($this->open_mentees as $mentee) {
                    if ($this->check_mentee_matches($mentee['id']) >= 1) {
                        continue;
                    }

                    foreach($this->open_mentors as $mentor) {
                        if ($this->check_mentor_matches($mentor['id']) >= $this->ratio) {
                            continue;
                        }

                        $match_points = 0;

                        foreach ( $this->matching_params as $key) {
                            if ($mentee['responses'][$key] == $mentor['responses'][$key]) {
                                $match_points++;
                            }
                        }

                        if ($match_points == $i) {
                            // make match
                            $confidence = $i!=0 ? $i / count($this->matching_params) : 0;
                            $this->make_match_pair($mentee['id'], $mentor['id'], $confidence);
                            break;
                        }
                    }
                }
            }
        }
        private function set_ratio() {
            $sql = "SELECT * FROM application WHERE match_form_id = :id AND m_type = 'mentor'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                ':id' => $this->match_form_id
            ));
            $mentors = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $sql = "SELECT * FROM application WHERE match_form_id = :id AND m_type = 'mentee'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                ':id' => $this->match_form_id
            ));
            $mentees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $float_ratio = count($mentors) && count($mentees) ? count($mentees) / count($mentors) : 0;
            $this->ratio = ceil($float_ratio);
        }
        private function set_matching_params() {
            $sql = "SELECT question_id as 'id' FROM question WHERE match_form_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                ':id' => $this->match_form_id
            ));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->matching_params = array();
            foreach($results as $question_id) {
                array_push($this->matching_params, $question_id['id']);
            }
        }
        private function set_open_applications() {
            $sql = "SELECT application_id, m_type, num_matches FROM application WHERE match_form_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                ':id' => $this->match_form_id
            ));
            $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($applicants as $applicant) {
                if($applicant['m_type'] == 'mentee') {
                    if($applicant['num_matches'] < 1) {
                        // TODO: The operations below are identical for mentors and mentees. Can be set aside in function
                        $applicant_data = array();
                        $applicant_data['id'] = $applicant['application_id'];
                        $applicant_data['responses'] = array();

                        $sql = "SELECT qr.question_id, qr.option_id
                                FROM question_response qr 
                                JOIN application a ON qr.application_id = a.application_id
                                WHERE a.match_form_id = :id
                                AND qr.application_id = :app_id";
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute(array(
                            ':id' => $this->match_form_id,
                            ':app_id' => $applicant['application_id']
                        ));
                        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach($responses as $response) {
                            $applicant_data['responses'][$response['question_id']] = $response['option_id'];
                        }

                        array_push($this->open_mentees, $applicant_data);
                    }
                } elseif ($applicant['m_type'] == 'mentor') {
                    if($applicant['num_matches'] < $this->ratio) {
                        $applicant_data = array();
                        $applicant_data['id'] = $applicant['application_id'];
                        $applicant_data['responses'] = array();

                        $sql = "SELECT qr.question_id, qr.option_id
                                FROM question_response qr 
                                JOIN application a ON qr.application_id = a.application_id
                                WHERE a.match_form_id = :id
                                AND qr.application_id = :app_id";
                        $stmt = $this->pdo->prepare($sql);
                        $stmt->execute(array(
                            ':id' => $this->match_form_id,
                            ':app_id' => $applicant['application_id']
                        ));
                        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach($responses as $response) {
                            $applicant_data['responses'][$response['question_id']] = $response['option_id'];
                        }

                        array_push($this->open_mentors, $applicant_data);
                    }
                }
            }
        }

        private function check_mentor_matches($mentor_id) {
            $sql = "SELECT COUNT(*) as 'COUNT'
                    FROM match_pair
                    WHERE mentor_application_id = :mentor_id
                    AND match_form_id = :match_form_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                ':match_form_id' => $this->match_form_id,
                ':mentor_id' => $mentor_id
            ));
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $response[0]['COUNT'];
        }

        private function check_mentee_matches($mentee_id) {
            $sql = "SELECT COUNT(*) as 'COUNT'
                    FROM match_pair
                    WHERE mentee_application_id = :mentee_id
                    AND match_form_id = :match_form_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                ':match_form_id' => $this->match_form_id,
                ':mentee_id' => $mentee_id
            ));
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $response[0]['COUNT'];
        }

        private function make_match_pair($mentee_id, $mentor_id, $confidence) {
            $sql = "INSERT INTO match_pair (match_form_id, mentee_application_id, mentor_application_id, confidence_rate)
                    VALUES (:match_form_id, :mentee_application_id, :mentor_application_id, :confidence)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                ':match_form_id' => $this->match_form_id,
                ':mentee_application_id' => $mentee_id,
                ':mentor_application_id' => $mentor_id,
                ':confidence' => $confidence
            ));

            //TODO: update num_matches in applicant table
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

