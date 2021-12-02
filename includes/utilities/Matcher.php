<?php
    class Matcher {
        private $pdo;
        public $matching_params = array();
        public $match_form_id;
        public $open_mentors = array();
        public $open_mentees = array();
        public $ratio;

        function __construct($id, $db_conn) {
            $this->match_form_id = $id;
            $this->pdo = $db_conn;
        }
        public function match_open_mentees() {  // see docs for algorithm sudo code
            $this->set_ratio();
            $this->set_matching_params();
            $this->set_open_applications();
                // if 0 return error


        }
        public function set_ratio() {
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
        public function set_matching_params() {
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
        public function set_open_applications() {
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
    }

