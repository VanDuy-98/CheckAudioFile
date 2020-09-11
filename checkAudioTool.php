<?php
include_once ("../VSII/userInterviewService.php");
include_once ("../VSII/MP3File.php");

class checkAudioTool extends userInterviewService
{
    private $_userInterviewService;

    public function __construct()
    {
        $this->_userInterviewService = new userInterviewService();
    }

    public function speakingTool()
    {
        $contents = array(
            array('user_interview_id', 'answers_id', 'grade', 'duration_time_audio', 'duration_time_db')
        );

        $userInterviewIds = $this->getUserInterviewIds();
        if ($userInterviewIds)
        {
            $userInterviewIds = array_chunk($userInterviewIds, 1000);
            foreach ($userInterviewIds as $key => $row)
            {
                $userDatas = $this->convertUserData($this->_userInterviewService->getUserData(implode($row,", ")));
                if($row)
                {
                    foreach ($row as $i => $userInterviewId)
                    {
                        $path = "C:/Users/duylv/Desktop/AudioData/user_interviews/$userInterviewId/answers/";

                        if (!file_exists($path))
                        {
                            continue;
                        }
                        $answersIds = array_slice(scandir($path), 2);
                        $answersIds = array_diff( $answersIds, ['.DS_Store'] );

                        foreach ($answersIds as $answersId)
                        {
                            $file = array_slice(scandir($path.$answersId), 2);
                            if (!isset($userDatas[$userInterviewId][$answersId]) || !isset($file[0]))
                            {
                                continue;
                            }


                            if ($file[0] == 'audio.mp3')
                            {
                                $handle = new MP3File($path.$answersId."/".$file[0]);
                                $duration = $handle->getDuration();
                            }
                            elseif ($file[0] == 'audio')
                            {
                                $duration = $this->wavDur($path.$answersId."/".$file[0]);
                            }

                            $durationTimeConfig = $userDatas[$userInterviewId][$answersId]['duration'];
                            $grade = $userDatas[$userInterviewId][$answersId]['grade'];

                            if ($duration > $durationTimeConfig)
                            {
                                $contents[] = array($userInterviewId, $answersId, $grade, $duration, $durationTimeConfig);
                            }

                            if (isset($userDatas[$userInterviewId]))
                            {
                                unset($userDatas[$userInterviewId]);
                            }
                        }
                    }
                }
            }
        }
        $file = fopen('../VSII/audioLog/durationTimeGreater1.csv', 'w');
        foreach ($contents as $content)
        {
            fputcsv($file, $content);
        }
        fclose($file);
    }

    private function getUserInterviewIds()
    {
        return array_slice(scandir("C:/Users/duylv/Desktop/AudioData/user_interviews/"), 2);
    }

    private function convertUserData($userDatas)
    {
        $datas = array();
        if(!$userDatas)
        {
            return $datas;
        }
        for ($i = 0; $i < count($userDatas); $i++)
        {
            $interviewResultId = $userDatas[$i]['interview_result_id'];
            $answersId = $userDatas[$i]['answers_id'];
            $grade = substr($userDatas[$i]['grade'], 6);
            $duration = $userDatas[$i]['duration'];


            $datas[$interviewResultId][$answersId] = array(
                'grade' => $grade,
                'duration' => $duration
            );
        }
        return $datas;
    }

    public function wavDur($file)
    {
        $fp = fopen($file, 'r');
        if (fread($fp,4) == 'RIFF')
        {
            fseek($fp, 20);
            $rawheader = fread($fp, 16);
            $header = unpack('vtype/vchannels/Vsamplerate/Vbytespersec/valignment/vbits',$rawheader);
            $pos = ftell($fp);
            while (fread($fp,4) != 'data' && !feof($fp))
            {
                $pos++;
                fseek($fp,$pos);
            }
            $rawheader = fread($fp, 4);
            $data = unpack('Vdatasize',$rawheader);
            $sec = $data['datasize']/$header['bytespersec'];
            //$minutes = (($sec / 60) % 60);
            //$seconds = ($sec % 60);
            $seconds = round($sec);
            return $seconds;
            //return str_pad($minutes,2,'0', STR_PAD_LEFT).':'.str_pad($seconds,2,'0', STR_PAD_LEFT);
        }
    }
}

$tool = new checkAudioTool();
$tool->speakingTool();




