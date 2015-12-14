<?php
require_once 'PHPExcel_1.8.0/Classes/PHPExcel.php';
require_once 'PHPExcel_1.8.0/Classes/PHPExcel/IOFactory.php';

class ExcelCreator
{
    private $path, $data;
    
    // ヘッダ
    private static $header = ['No', 'タイトル', '起票日', '更新日', '担当者', 'ステータス', 'ラベル', 'リリース予定'];

    /**
     * コンストラクタ
     */
    public function __construct($data)
    {
        $this->path = dirname(__FILE__) . '/issues.xls';
        $this->data = $data;
    }

    /**
     * Excelにデータを出力し、ダウンロードする
     */
    public function generate()
    {
        $excel = new PHPExcel();

        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        $sheet->setTitle('課題管理表');

        // ヘッダ設定
        foreach (self::$header as $key => $val) {
            $sheet->setCellValueByColumnAndRow($key, 1, $val);
        }

        // データ設定
        foreach ($this->data as $key => $issue) {
            $row = $key + 2;
            $sheet->setCellValueByColumnAndRow(0, $row, $issue['No']);
            $sheet->setCellValueByColumnAndRow(1, $row, $issue['Title']);
            $sheet->setCellValueByColumnAndRow(2, $row, $issue['Created']);
            $sheet->setCellValueByColumnAndRow(3, $row, $issue['Updated']);
            $sheet->setCellValueByColumnAndRow(4, $row, $issue['Assignee']);
            $sheet->setCellValueByColumnAndRow(5, $row, $issue['State']);
            $sheet->setCellValueByColumnAndRow(6, $row, $issue['Labels']);
            $sheet->setCellValueByColumnAndRow(7, $row, $issue['Milestone']);
        }

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save($this->path);

        // 出力
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . basename($this->path) . ';');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($this->path));
        readfile($this->path);
        unlink($this->path);
        exit();
    }
}
