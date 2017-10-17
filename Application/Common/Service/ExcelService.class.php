<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 15:09
 */
namespace Common\Service;
use Mockery\CountValidator\Exception;

class ExcelService{

      /**
       * 加载PHPExcel.php
       */
    protected $PHPExcelPath;

    public function __construct(){
//        parent::__construct();
        $this->PHPExcelPath = LIB_PATH.'Com/PHPExcel/';
    }

    public function LoadPHPExcel()
      {
          import('PHPExcel',$this->PHPExcelPath);
      }
      /**
       * 加载 IOFactory类
       */
      public function LoadFileIoFactory()
      {
          import('IOFactory',$this->PHPExcelPath.'PHPExcel','.php');
      }

      /**
       * 加载Excel5.php
       */
      public function LoadExcel5Reader()
      {
          import('Excel5',$this->PHPExcelPath.'PHPExcel/Reader','.php');
      }

    public function LoadExcel5Writer(){
        import('Excel5',$this->PHPExcelPath.'PHPExcel/Writer','.php');
    }

    public function excel2array($file){
        $this->LoadPHPExcel();
        $objReader=new \PHPExcel_Reader_Excel5();
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file);
        $test = $objPHPExcel->getAllSheets();
        foreach($test as $k=>$v){
            $title = $v->getTitle();
            if($title == 'teachers'){
                $tData = $v->toArray();
            }
            if($title == 'students'){
                $sData = $v->toArray();
            }
        }

        is_array($sData)?$array['sData'] = $sData:'';
        is_array($tData)?$array['tData'] = $tData:'';

        return $array;
    }

  }