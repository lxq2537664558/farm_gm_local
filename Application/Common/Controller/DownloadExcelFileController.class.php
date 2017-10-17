<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/22
 * Time: 10:41
 */
namespace Common\Controller;
use Think\Controller;
use Common\Service\ExcelService;

class DownloadExcelFileController extends Controller{

    protected $ExcelService;
    protected $columns;

    public function __construct(){

        parent::__construct();
//echo 1;exit;
        $this->columns = C('PHPExcelColumns');
        $this->ExcelService = new ExcelService();
        $this->ExcelService->LoadPHPExcel();
    }
    
    public function getTeacherExcelFile(){

    }

    public function createExcelFile($sheetTitle,array $title,array $data,$sheet=0){

        $objPHPExcel = new \PHPExcel();
        // 设置文件的一些属性，在xls文件——>属性——>详细信息里可以看到这些值，xml表格里是没有这些值的
        $objPHPExcel
            ->getProperties()  //获得文件属性对象，给下文提供设置资源
            ->setCreator( "SUNDATA")                 //设置文件的创建者
            ->setLastModifiedBy( "SUNDATA")          //设置最后修改者
            ->setTitle( "Student Information" )    //设置标题
            ->setSubject( "Student Information" )  //设置主题
            ->setDescription( "Student Information.") //设置备注
            ->setKeywords( "Student Information")        //设置标记
            ->setCategory( "Test result file");                //设置类别

        //设置第一张工作表为操作表，并初始化该表数据，如表头，表数据等
        $objPHPExcel->setActiveSheetIndex($sheet);
        $activeSheet = $objPHPExcel->getActiveSheet();
        $activeSheet->setTitle($sheetTitle);
        //设置行高为 20
        $activeSheet->getDefaultRowDimension()->setRowHeight(20);
        // 给表格添加Title
        $columns = $this->columns;
        if($title) {
            foreach ($title as $k => $v) {
                $activeSheet->setCellValue($columns[$k].'1', $v);//给表的单元格设置表头
            }
        }
        
        //给表初始化数据
        if($data){
            foreach ($data as $dk=>$dv){
                foreach ($dv as $ck=>$cv) {
                    
                    // 如果是多array的数据 设置下拉选择
                    if (is_array($cv) && count($cv) > 0) {
                        $select_data = implode(",", $cv);
                        $objValidation = $activeSheet->getCell($columns[$ck].($dk+2))->getDataValidation(); //这一句为要设置数据有效性的单元格
                        $objValidation -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
                        -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
                        -> setAllowBlank(false)
                        -> setShowInputMessage(true)
                        -> setShowErrorMessage(true)
                        -> setShowDropDown(true)
                        -> setErrorTitle('输入的值有误')
                        -> setFormula1('"'.$select_data.'"');
                        $activeSheet->setCellValueExplicit($columns[$ck].($dk+2),$cv[0], \PHPExcel_Cell_DataType::TYPE_STRING);
                    } else {
                        //以字符串格式输出所有数据，避免长数字被格式化成科学技术法 如 身份证号
                        $activeSheet->setCellValueExplicit($columns[$ck].($dk+2),$cv, \PHPExcel_Cell_DataType::TYPE_STRING);
                        $activeSheet->getStyle($columns[$ck].($dk+2))->getNumberFormat()->setFormatCode("@");
                    }
                }
            }
        }
        //设置表标题
        $activeSheet->setTitle($sheetTitle);

        return $objPHPExcel;
    }

}