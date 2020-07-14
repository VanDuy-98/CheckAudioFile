<?php
class userInterviewRepository
{
    public function getUserData($userInterviewID)
    {
//        $query = $this->_mysqlDriver->select("ui.id as interview_result_id, ise.id as answers_id, i.grade, ise.duration")
//            ->from('user_interview ui')
//            ->join('interview i', 'i.id = ui.interview_id')
//            ->join('interview_script_element ise', 'ise.interview_id = i.id')
//            ->where("ise.type = 'record' AND ui.id IN(".$userInterviewID.")")
//            ->group_by('ui.id, ise.id')
//            ->get();
//        if($query->num_rows() > 0)
//        {
//            $data = $query->result();
//            return $data;
//        }
//        return NULL;
        $dsn = "mysql:host=localhost;dbname=jiem";
        $username = "root";
        $password = "";

        try {
            $conn = new PDO($dsn, $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT ui.id as interview_result_id, ise.id as answers_id, i.grade, ise.duration
                FROM user_interview ui
                INNER JOIN interview i ON i.id = ui.interview_id
                INNER JOIN interview_script_element ise ON ise.interview_id = i.id
                WHERE ise.type = 'record' AND ui.id IN(".$userInterviewID.")
                GROUP BY ui.id, ise.id";

            $statement = $conn->prepare($sql);
            $statement->execute();

            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $statement->fetchAll();
            return $result;
        }
        catch(PDOException $error)
        {
            echo $sql . "<br>" . $error->getMessage();
        }
    }
}
