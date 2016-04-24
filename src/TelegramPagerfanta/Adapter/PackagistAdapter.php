<?php
namespace TelegramPagerfanta\Adapter;
use Pagerfanta\Adapter\AdapterInterface;
use Base32\Base32;
class PackagistAdapter implements AdapterInterface
{
    private $results;

    /**
     * Constructor.
     *
     * @param array $array The array.
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    public function getNbResults()
    {
        return $this->results['total'];
    }

    public function getSlice($offset, $length)
    {
        return array_slice($this->results['results'], $offset, $length);
    }
    
    public function getPageContent($pagerfanta, $query) {
        $offset = ($pagerfanta->getCurrentPage() - 1) * $pagerfanta->getMaxPerPage();
        if($offset >= count($this->results['results'])) {
            $offset = $offset % count($this->results['results']);
        }
        $length = $pagerfanta->getMaxPerPage();
        $results = array_slice($this->results['results'], $offset, $length);
        $text = "<b>Showing results for '$query'</b>";
        foreach($results as $result) {
            if(strlen($result['description']) > 66) {
                $result['description'] = substr($result['description'], 1, 66) . '...';
            }
            $encoded = rtrim(Base32::encode(gzdeflate($result['name'], 9)), '=');
            $text.=
                "\n\n".
                "<b>{$result['name']}</b>\n".
                ($result['description'] ? $result['description'] . "\n" : '').
                "<i>View: </i>/v_".$encoded;
        }
        return $text;
    }
}