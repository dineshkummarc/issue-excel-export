<?php
require_once 'GithubIssue.php';
require_once 'ExcelCreator.php';

$issue = new GithubIssue('{owner}', '{repo}', '{user}', '{token}');
$data = $issue->process();

$ec = new ExcelCreator($data);
$ec->generate();
