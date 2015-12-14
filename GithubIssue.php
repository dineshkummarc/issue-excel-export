<?php
class GithubIssue
{
    const END_POINT = 'https://api.github.com/repos/%1$s/%2$s/issues?filter=all&state=all&sort=created&direction=asc';
    private $owner, $repo, $user, $token;
    private $issues;

    /**
     * コンストラクタ
     */
	public function __construct($owner, $repo, $user, $token)
    {
        $this->owner    = $owner;
        $this->repo     = $repo;
        $this->user     = $user;
        $this->token    = $token;
    }

    /**
     * メイン処理
     */
    public function process()
    {
        $this->getDataFromGithub();
        $res = $this->formatIssueData();

        return $res;
    }

    /**
     * GitHub APIでデータをIssueデータを取得
     */
    private function getDataFromGithub()
    {
        $params = [
            'filter'    => 'all',
            'state'     => 'all',
            'sort'      => 'created',
            'direction' => 'asc'
        ];
        $query = http_build_query($params);
        $endpoint = sprintf(self::END_POINT, $this->owner, $this->repo) . $query;

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->user}:{$this->token}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        
        $this->issues = json_decode($res);
    }

    /**
     * Issueデータを整形して返す
     */
    private function formatIssueData()
    {
        $res = [];
        $issues = $this->issues;
        foreach ($issues as $issue) {
            $temp = [];
            $temp['No']         = $issue->number;
            $temp['Title']      = $issue->title;
            $temp['Labels']     = $issue->labels[0]->name; // ラベルが複数の場合に非対応
            $temp['State']      = $issue->state;
            $temp['Assignee']   = $issue->assignee->login;
            $temp['Milestone']  = $issue->milestone->title;
            $temp['Created']    = $issue->created_at;
            $temp['Updated']    = $issue->updated_at;
            $res[] = $temp;                        
        }
        return $res;
    }
}
