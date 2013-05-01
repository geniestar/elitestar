<?php
$pageCount = 20;
$currentPage = isset($_GET['page'])?intval($_GET['page']):0;
if (!$currentPage)
{
    $currentPage = 0;
}

//$totalPage = floor($data['total']/$pageCount) + 1;

$totalPage = 7;

$data['pagenations'] = array();
for ($i=0; $i < $totalPage ;$i++)
{
    $isSelected = ($i === $page)?true:false;
    $data['pagenations'][] = array('name' => $i+1, 'url' => EliteHelper::replaceUrlParam($data['url'], 'page', $i), 'selected' => $isSelected);
}

if ($currentPage != 0)
{
    $data['prevPageUrl'] = EliteHelper::replaceUrlParam($data['url'], 'page', $currentPage - 1);
}
if ($currentPage != $totalPage)
{
    $data['nextPageUrl'] = EliteHelper::replaceUrlParam($data['url'], 'page', $currentPage + 1);
}

?>
