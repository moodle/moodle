<?php
require_once __DIR__ . '/autoload.php';


class JupyterCommit
{
    private $client;

    private $repositoryuser;
    private $repositoryname;
    private $repositorybranch;
    private $repositorytoken;


    /**
     * JupyterCommit constructor.
     */
    public function __construct()
    {
        $this->repositoryuser = get_config('mod_jupyternotebook', 'repositoryuser');
        $this->repositoryname = get_config('mod_jupyternotebook', 'repositoryname');
        $this->repositorybranch = get_config('mod_jupyternotebook', 'repositorybranch');
        $this->repositorytoken = get_config('mod_jupyternotebook', 'repositorytoken');

        $this->client = new Github\Client();
        $this->client->authenticate($this->repositorytoken, \Github\Client::AUTH_HTTP_TOKEN);
    }

    public function commitandpush($path, $content, $commitMessage){
        $fileExists = $this->client->api('repo')->contents()->exists($this->repositoryuser, $this->repositoryname, $path);
        if($fileExists){
            $oldFile = $this->client->api('repo')->contents()->show($this->repositoryuser, $this->repositoryname, $path, $this->repositorybranch);
            $this->client->api('repo')->contents()->update($this->repositoryuser, $this->repositoryname, $path, $content, $commitMessage, $oldFile['sha'], $this->repositorybranch);
        }else{
            $this->client->api('repo')->contents()->create($this->repositoryuser,$this->repositoryname, $path,$content, $commitMessage, $this->repositorybranch);
        }
    }
}
