<?php
$pageCount = 20;
$currentPage = isset($_GET['page'])?intval($_GET['page']):1;
if (!$currentPage)
{
    $currentPage = 1;
}
$totalPage = floor($data['total']/$pageCount) + 1;

$data['pagenations'] = array();
for ($i=0; $i < $totalPage ;$i++)
{
    $isSelected = ($i === $page)?true:false;
    if ($i+1 != $currentPage)
    {
        $data['pagenations'][] = array('name' => $i + 1, 'url' => EliteHelper::replaceUrlParam($data['url'], 'page', $i + 1), 'selected' => $isSelected);
    }
    else
    {
        $data['pagenations'][] = array('name' => $i + 1, 'url' => '', 'selected' => $isSelected);
    }
}

if ($currentPage != 1)
{
    $data['prevPageUrl'] = EliteHelper::replaceUrlParam($data['url'], 'page', $currentPage - 1);
}
if ($currentPage != $totalPage)
{
    $data['nextPageUrl'] = EliteHelper::replaceUrlParam($data['url'], 'page', $currentPage + 1);
}

?>
