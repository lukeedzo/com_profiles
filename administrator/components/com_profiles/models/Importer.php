<?php

JLoader::register('ProfilesHelper', JPATH_COMPONENT_ADMINISTRATOR .
    DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'profiles.php');

require '../administrator/components/com_profiles/helpers/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class Importer
{

    /**
     * Import data from an xlsx file into the database
     *
     * This function imports data from an xlsx file into the database using PHPExcel library.
     *
     * @throws Exception If an error occurs while reading the xlsx file
     */
    public function xlsxImport()
    {
        // Load Helper class
        $helper = new ProfilesHelper();
        // Load xlsx file
        $spreadsheet = IOFactory::load('../administrator/components/com_profiles/models/static/list.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        // Specify columns to retrieve
        $columns = array(
            'A' => 'name',
            'B' => 'surname',
            'C' => 'degree',
            'D' => 'position_1',
            'E' => 'position_2',
            'F' => 'position_3',
            'G' => 'position_4',
            'H' => 'position_5',
            'I' => 'e_mail',
            'J' => 'publication_list',
            'K' => 'external_orcid_profile',
            'L' => 'external_web_science',
            'M' => 'external_scopus',
            'N' => 'external_google_sholar',
            'O' => 'external_research_gate',
            'P' => 'external_academia_edu',
            'Q' => 'external_others',
            'R' => 'public',
        );

        // Get data from xlsx file and store in an array
        $data = array();
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $rowData = array();
            foreach ($columns as $column => $columnName) {
                $cell = $sheet->getCell($column . $row->getRowIndex());
                $value = $cell->getCalculatedValue();
                $rowData[$columnName] = $value;
            }
            if (!empty($rowData['name'])) {
                $data[] = (object) $rowData;
            }
        }

        // Remove first object from $data array
        array_shift($data);

        $fields = [
            'name',
            'degree',
            'e_mail',
            'positions',
            'publication_list',
            'external_profiles',
            'state',
        ];

        foreach ($data as $profile) {
            $fullName = $profile->surname . ' ' . $profile->name;
            $id = $helper->getIdByName($fullName);

            $values = [
                $fullName,
                $profile->degree,
                $profile->e_mail,
                $helper->positionsEncode([
                    $profile->position_1,
                    $profile->position_2,
                    $profile->position_3,
                    $profile->position_4,
                    $profile->position_5,
                ]),
                $helper->publicationsEncode([(object) [
                    'publication' => $profile->publication_list,
                    'publication_url' => 'https://www.test/?aut=' . $profile->name . '%' . $profile->surname,
                ]]),
                $helper->externalProfilesEncode([
                    $helper->removeStingTabs($profile->external_orcid_profile),
                    $helper->removeStingTabs($profile->external_web_science),
                    $helper->removeStingTabs($profile->external_scopus),
                    $helper->removeStingTabs($profile->external_google_sholar),
                    $helper->removeStingTabs($profile->external_research_gate),
                    $helper->removeStingTabs($profile->external_academia_edu),
                    $helper->removeStingTabs($profile->external_others),
                ]),
                (int) ($profile->public === 'Taip'),
            ];

            if ($id) {
                $helper->update($fields, $values, '#__profiles_list', ['id' => $id]);
            } else {
                $helper->insert($fields, $values, '#__profiles_list');
            }
        }
    }

}
