<?php

/* @author Klethônio Ferreira */
include './dts/dbaSis.php';
include './dts/getSis.php';
include './dts/othSis.php';
include './Classes/PHPExcel.php';

$exer = $_GET['exer'];
$list = $_GET['list'];
$classId = $_GET['classid'];
if ($readClass = read('act_config_class', 'WHERE id=?', [$classId])) {

    $class = $readClass[0];
    $urlGet = $class['period'] . '/turma-' . $class['class'];
}
if (!$readClass) {
    header('Location: ' . BASE);
} elseif ($exer == 'all' || !$exer) {
    if (preg_match('/^(all)\-/i', $list) || !$list) {
        $readLists = read('act_lists', 'WHERE class_id=? ORDER BY num ASC', [$class['id']]);
    } else {
        $readLists = read('act_lists', 'WHERE class_id=? AND id=? ORDER BY num ASC', [$class['id'], $list]);
    }
    if (!$readLists) {
        header('Location: ' . BASE . '/' . $urlGet . '/atividades/gerar-relatorio');
    } else {
        if (!$readUsers = read('act_users', 'WHERE class_id=? AND level=0 AND status=1 ORDER BY name', [$class['id']])) {
            echo 'Erro: Nenhum aluno cadastrado! Redirecionado...';
            header('Refresh: 3; url=' . BASE . '/' . $urlGet . '/atividades/gerar-relatorio');
            exit();
        } else {
            $objPHPExcel = new PHPExcel();
            PHPExcel_Settings::setLocale('pt_br');
            $indexList = 0;
            foreach ($readLists as $list) {
                $ordereds = array();
                if ($readOrdered = read('act_ordered', 'WHERE list_id=? ORDER BY num', [$list['id']])) {
                    $orderExists = TRUE;
                    if ($indexList > 0) {
                        $objPHPExcel->createSheet();
                    }
                    $hideInfor = TRUE;
                    foreach ($readOrdered as $ordered) {
                        $ordereds[$ordered['num']] = $ordered['id'];
                    }
                    $lists[] = $list['num'];
                    $professor = getUser($class['professor_id']);
                    $nameProfessor = explode(' ', $professor['name']);
                    $nameProfessor = $nameProfessor[0] . ' ' . $nameProfessor[1];
                    $monitors = 'Sem monitores.';
                    if ($readMonitors = read('act_users', 'WHERE class_id=? AND level=1', [$class['id']])) {
                        $monitors = array();
                        foreach ($readMonitors as $monitor) {
                            $nameMonitor = explode(' ', $monitor['name']);
                            $monitors[] = $nameMonitor[0] . ' ' . $nameMonitor[1];
                        }
                        $monitors = implode(', ', $monitors);
                    }
                    $objPHPExcel->setActiveSheetIndex($indexList)
                            ->setCellValue('B1', 'ICC - Turma ' . $class['class'] . ' - ' . $class['period'] . ';  Prof. ' . $nameProfessor . '; Monitores: ' . $monitors)
                            ->setCellValue('B2', 'Lista ' . $list['num'] . ' - ' . $list['description'])
                            ->setCellValue('B3', 'Matrícula')
                            ->setCellValue('C3', 'Nome');
                    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
                    $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('D:Z')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_GENERAL);
                    $objPHPExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_GENERAL);
                    $objPHPExcel->getActiveSheet()->freezePaneByColumnAndRow(3, 4);

                    $collumn = 3;
                    foreach ($ordereds as $num => $ordered) {
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($collumn, 3, 'Quest. ' . $num);
                        $collumn++;
                    }
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($collumn, 3, 'Média');
                    $i = 1;
                    foreach ($readUsers as $user) {
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, ($i + 3), $i);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, ($i + 3), $user['register']);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($i + 3), $user['name']);
                        $collumn = 3;
                        foreach ($ordereds as $ordered) {
                            $grade = getSent($user['id'], $ordered, 'grade');
                            $grade = $grade === NULL ? 'Pend.' : ($grade === FALSE ? '' : $grade);
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($collumn, ($i + 3), $grade);
                            $lastCollumn = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($collumn, ($i + 3))->getColumn();
                            $lastCell = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($collumn, ($i + 3))->getCoordinate();
                            $cellAvangere_1 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($collumn + 1)->getColumn() . '4';
                            $cellAvangere_2 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($collumn + 1, ($i + 3))->getCoordinate();
                            $coordinateClassAvangere = $cellAvangere_1 . ':' . $cellAvangere_2;
                            $collumn++;
                        }
                        $coordinate = 'D' . ($i + 3) . ':' . $lastCollumn . ($i + 3);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($collumn, ($i + 3), '=IF(COUNT(' . $coordinate . ') <> 0, SUM(' . $coordinate . ')/(COUNT(' . $coordinate . ')+COUNTBLANK(' . $coordinate . ')),0)');
                        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($collumn, ($i + 3))->getNumberFormat()->setFormatCode('0.00');
                        $i++;
                    }
                    $objPHPExcel->getActiveSheet()->setTitle('Lista ' . $list['num']);

                    $indexList++;
                }
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12.14);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

                $infoCollumn = $i + 6;
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $infoCollumn, "=CONCATENATE(\"Faltando: \", COUNTBLANK(D4:{$lastCell}))");
                $infoCollumn++;
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $infoCollumn, "=CONCATENATE(\"Corrigidos: \", COUNT(D4:{$lastCell}))");
                $infoCollumn++;
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $infoCollumn, "=CONCATENATE(\"Maior Média: \", TEXT(MAX({$coordinateClassAvangere}), \"0,00\"))");
                $infoCollumn++;
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $infoCollumn, "=CONCATENATE(\"Média da Turma: \", TEXT(AVERAGE({$coordinateClassAvangere}), \"0,00\"))");
                $objPHPExcel->getActiveSheet()->getStyle('C' . $infoCollumn)->getNumberFormat()->setFormatCode('0.00');
            }
        }
    }

    if (!$orderExists) {
        echo 'Alerta: Nenhum exercicio requisitado nessa categoria! Redirecionado...';
        header('Refresh: 3; url=' . BASE . '/' . $urlGet . '/atividades/gerar-relatorio');
        exit();
    }

    // CabeÃ§alho do arquivo para ele baixar
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="ICC T' . $class['class'] . ' ' . $class['period'] . ' - LISTA ' . implode(', ', $lists) . ' - MATLAB.xlsx"');
    header('Cache-Control: max-age=0');
    // Se for o IE9, isso talvez seja necessÃ¡rio
    header('Cache-Control: max-age=1');

    $objPHPExcel->setActiveSheetIndex(0);
    // Acessamos o 'Writer' para poder salvar o arquivo
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

    // Salva diretamente no output, poderÃ­amos mudar arqui para um nome de arquivo em um diretÃ³rio ,caso nÃ£o quisessemos jogar na tela
    $objWriter->save('php://output');
    exit;
} else {
    if (preg_match('/^(all)\-/i', $list) || !$list) {
        $readLists = read('act_lists', 'WHERE class_id=? AND num_exe>=? ORDER BY num ASC', [$class['id'], $exer]);
    } else {
        $readLists = read('act_lists', 'WHERE class_id=? AND id=? AND num_exe>=? ORDER BY num ASC', [$class['id'], $list, $exer]);
    }
    if (!$readLists) {
        header('Location: ' . BASE . '/' . $urlGet . '/atividades/gerar-relatorio');
    } else {
        if (!$readUsers = read('act_users', 'WHERE class_id=? AND level=0 AND status=1 ORDER BY name', [$class['id']])) {
            echo 'Erro: Nenhum aluno cadastrado! Redirecionado...';
            header('Refresh: 3; url=' . BASE . '/' . $urlGet . '/atividades/gerar-relatorio');
            exit();
        } else {
            $objPHPExcel = new PHPExcel();
            PHPExcel_Settings::setLocale('pt_br');
            $indexList = 0;
            foreach ($readLists as $list) {
                if ($readOrdered = read('act_ordered', 'WHERE list_id=? AND num=?', [$list['id'], $exer])) {
                    if ($indexList > 0) {
                        $objPHPExcel->createSheet();
                    }
                    $orderExists = TRUE;
                    $lists[] = $list['num'];

                    $professor = getUser($class['professor_id']);
                    $nameProfessor = explode(' ', $professor['name']);
                    $nameProfessor = $nameProfessor[0] . ' ' . $nameProfessor[1];
                    $monitors = 'Sem monitores.';
                    if ($readMonitors = read('act_users', 'WHERE class_id=? AND level=1', [$class['id']])) {
                        $monitors = array();
                        foreach ($readMonitors as $monitor) {
                            $nameMonitor = explode(' ', $monitor['name']);
                            $monitors[] = $nameMonitor[0] . ' ' . $nameMonitor[1];
                        }
                        $monitors = implode(', ', $monitors);
                    }
                    $ordered = $readOrdered[0];
                    $objPHPExcel->setActiveSheetIndex($indexList)
                            ->setCellValue('B1', 'ICC - Turma ' . $class['class'] . ' - ' . $class['period'] . ';  Prof. ' . $nameProfessor . '; Monitores: ' . $monitors)
                            ->setCellValue('B2', 'Lista ' . $ordered['list_num'] . ' - Ex ' . str_pad($ordered['num'], 2, '0', STR_PAD_RIGHT) . ' - ' . $ordered['title'])
                            ->setCellValue('B3', 'Matrícula')
                            ->setCellValue('C3', 'Nome')
                            ->setCellValue('D3', 'Notas')
                            ->setCellValue('E3', 'Comentários');
                    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
                    $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_GENERAL);
                    $objPHPExcel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_GENERAL);
                    $objPHPExcel->getActiveSheet()->freezePaneByColumnAndRow(3, 4);
                    $i = 1;
                    foreach ($readUsers as $user) {
                        $sent = getSent($user['id'], $ordered['id']);
                        $grade = !$sent ? '' : ($sent['grade'] === NULL ? 'Pend.' : $sent['grade']);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, ($i + 3), $i);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, ($i + 3), $user['register']);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($i + 3), $user['name']);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($i + 3), $grade);
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, ($i + 3), $sent['notes']);
                        $i++;
                    }
                    $objPHPExcel->getActiveSheet()->setTitle('Lista ' . $list['num']);
                    $indexList++;
                }
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12.14);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            }
        }
    }
    if (!$orderExists) {
        echo 'Alerta: Nenhum exercicio requisitado nessa categoria! Redirecionado...';
        header('Refresh: 3; url=' . BASE . '/' . $urlGet . '/atividades/gerar-relatorio');
        exit();
    }
    if (count($lists) == 1) {
        $list = 'L' . $lists[0] . '_';
    } else {
        $list = '';
    }
    // CabeÃ§alho do arquivo para ele baixar
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="ICC T' . $class['class'] . ' ' . $class['period'] . ' - ' . $list . 'Ex' . str_pad($exer, 2, '0', STR_PAD_LEFT) . ' - MATLAB.xlsx"');
    header('Cache-Control: max-age=0');
    // Se for o IE9, isso talvez seja necessÃ¡rio
    header('Cache-Control: max-age=1');

    $objPHPExcel->setActiveSheetIndex(0);
    // Acessamos o 'Writer' para poder salvar o arquivo
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

    // Salva diretamente no output, poderÃ­amos mudar arqui para um nome de arquivo em um diretÃ³rio ,caso nÃ£o quisessemos jogar na tela
    $objWriter->save('php://output');
    exit;
}