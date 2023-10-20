<?php

require_once(__DIR__ . '/../../config.php');
require_login();

$courseid = required_param('id', PARAM_INT);
$type = optional_param('type','image',PARAM_TEXT);
global $CFG, $USER, $SITE, $DB, $DB2;

$blockPath = $CFG->dirroot . '/blocks/moodle_certificate';

$layoutPath = $blockPath . '/images/layout.png';
$fontRegular = $blockPath . '/fonts/SecuritasProOffice-Regular.ttf';
$fontBold = $blockPath . '/fonts/SecuritasProOffice-Bold.ttf';

$amsterdamOneFont = $blockPath . '/fonts/AmsterdamOne.ttf';
$montseratFont = $blockPath . '/fonts/Montserrat-Regular-400.ttf';
$roxboroughFont = $blockPath . '/fonts/RoxboroughCF.ttf';

$course = $DB->get_records_sql('SELECT c.fullname
                                    FROM {course_completions} cc
                                    JOIN {course} c ON c.id = cc.course
                                    AND cc.userid = ' . $USER->id . ' 
                                    AND cc.course = ' . $courseid);

$image = imagecreatefrompng($layoutPath);
$brownColor = imagecolorallocate($image, 183, 142, 66);
$blackColor = imagecolorallocate($image, 0, 0, 0);

$titleFontSize = 125;
$courseNameFontSize = 60;
$participantFontSize = 70;
$subTextFontSize = 30;

$courseName = array_keys($course)[0];

$text = get_config('block_moodle_certificate')->title;
$x = getXOfText($roxboroughFont, $titleFontSize, $text, $image);
imagettftext($image, $titleFontSize, 0, $x, 440, $brownColor, $roxboroughFont, $text);

$text = mb_strtoupper($courseName);
$x = getXOfText($montseratFont, $courseNameFontSize, $text, $image);
imagettftext($image, $courseNameFontSize, 0, $x, 600, $blackColor, $montseratFont, $text);

$text = $USER->firstname . " " .$USER->lastname;
$x = getXOfText($amsterdamOneFont, $participantFontSize, $text, $image);
imagettftext($image, $participantFontSize, 0, $x, 800, $brownColor, $amsterdamOneFont, $text);

$text = get_config('block_moodle_certificate')->subtext;
$x = getXOfText($montseratFont, $subTextFontSize, $text, $image);
imagettftext($image, $subTextFontSize, 0, $x, 970, $blackColor, $montseratFont, $text);

$format = getDateFormatFromTimezone(core_date::get_user_timezone($USER));
$text = date($format, time());
$x = getXOfText($montseratFont, $subTextFontSize, $text, $image);
imagettftext($image, $subTextFontSize, 0, $x, 1200, $brownColor, $montseratFont, $text);

$text = $SITE->fullname;
$x = getXOfText($montseratFont, $subTextFontSize, $text, $image);
imagettftext($image, $subTextFontSize, 0, $x, 1300, $blackColor, $montseratFont, $text);

if($type === 'pdf')
{
    require_once($CFG->libdir . '/pdflib.php');
    ob_start();
    imagepng($image);
    $imageData = ob_get_contents();
    ob_end_clean();

    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(TRUE, 0);

    $pdf->AddPage();

    $pdf->Image('@' . $imageData, 0,0,297, 210);
    $pdf->Output('certificate.pdf', 'I');

}
else{
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=test.png');
    header('Content-Transfer-Encoding: binary');
    header('Connection: Keep-Alive');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    imagepng($image);
    imagedestroy($image);
}

function getXOfText($font, $size, $text, $image)
{
    $textBox = imagettfbbox($size, 0, $font, $text);
    $textWidth = $textBox[2] - $textBox[0];  // calculate text width

    return (imagesx($image) - $textWidth) / 2;
}

function getDateFormatFromTimezone($timezone) :string {
    $zone = new DateTimeZone($timezone);
    $region = explode('/', $zone->getName())[0];

    // List of countries using MM/DD/YYYY format
    $mdyCountries = ['America', 'Philippines'];

    // List of countries using YYYY/MM/DD format
    $ymdCountries = ['Asia', 'Japan', 'Korea'];

    if (in_array($region, $mdyCountries)) {
        return 'm.d.Y';
    } elseif (in_array($region, $ymdCountries)) {
        return 'Y.m.d';
    } else {
        return 'd.m.Y'; // default format for most of the world
    }
}
