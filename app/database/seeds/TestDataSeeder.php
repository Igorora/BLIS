<?php

class TestDataSeeder extends DatabaseSeeder {

    public function run() {


        /* Users table */
        $usersData = array(
            array(
                "username" => "administrator", "password" => Hash::make("password"), "email" => "admin@chub.org",
                "name" => "chublmis Administrator", "designation" => "Programmer"
            ),
            array(
                "username" => "external", "password" => Hash::make("password"), "email" => "admin@kblis.org",
                "name" => "External System User", "designation" => "Administrator", "image" => "/i/users/user-2.jpg"
            ),
        );

        foreach ($usersData as $user) {
            $users[] = User::create($user);
        }
        $this->command->info('users seeded');


        /* Specimen Types table */
        $specTypesData = array(
            array("name" => "Ascitic Tap"),
            array("name" => "Aspirate"),
            array("name" => "CSF"),
            array("name" => "Dried Blood Spot"),
            array("name" => "High Vaginal Swab"),
            array("name" => "Nasal Swab"),
            array("name" => "Plasma"),
            array("name" => "Plasma EDTA"),
            array("name" => "Pleural Tap"),
            array("name" => "Pus Swab"),
            array("name" => "Rectal Swab"),
            array("name" => "Semen"),
            array("name" => "Serum"),
            array("name" => "Skin"),
            array("name" => "Sputum"),
            array("name" => "Stool"),
            array("name" => "Synovial Fluid"),
            array("name" => "Throat Swab"),
            array("name" => "Urethral Smear"),
            array("name" => "Urine"),
            array("name" => "Vaginal Smear"),
            array("name" => "Water"),
            array("name" => "Whole Blood"),
        );

        foreach ($specTypesData as $specimenType) {
            $specTypes[] = SpecimenType::create($specimenType);
        }
        $this->command->info('specimen_types seeded');

        /* Test Categories table - These map on to the lab sections */
        $test_categories = TestCategory::create(array("name" => "PARASITOLOGY", "description" => ""));
        $lab_section_microbiology = TestCategory::create(array("name" => "MICROBIOLOGY", "description" => ""));

        $this->command->info('test_categories seeded');
        /*         * Panel Seed* */
        $panels = Panel::create(array('name' => 'Panel X', 'description' => ""));
        $panels_testType = Panel::create(array("name" => "Panel Y", "description" => ""));
        $this->command->info('Panel table seeded');

        /* Measure Types */
        $measureTypes = array(
            array("id" => "1", "name" => "Numeric Range"),
            array("id" => "2", "name" => "Alphanumeric Values"),
            array("id" => "3", "name" => "Autocomplete"),
            array("id" => "4", "name" => "Free Text"),
            array("id" => "5", "name" => "Large Text"),
            array("id" => "6", "name" => "Date picker"),
            array("id" => "7", "name" => "Time picker"),
            array("id" => "8", "name" => "Diagnoses autocomplete"),
            array("id" => "9", "name" => "Medicines autocomplete"),
            array("id" => "10", "name" => "Symptoms autocomplete"),
            array("id" => "11", "name" => "Signs autocomplete")
        );

        foreach ($measureTypes as $measureType) {
            MeasureType::create($measureType);
        }
        $this->command->info('measure_types seeded');



        /* Test Phase table */
        $test_phases = array(
            array("id" => "1", "name" => "Pre-Analytical"),
            array("id" => "2", "name" => "Analytical"),
            array("id" => "3", "name" => "Post-Analytical")
        );
        foreach ($test_phases as $test_phase) {
            TestPhase::create($test_phase);
        }
        $this->command->info('test_phases seeded');

        /* Test Status table */
        $test_statuses = array(
            array("id" => "1", "name" => "not-received", "test_phase_id" => "1"), //Pre-Analytical
            array("id" => "2", "name" => "pending", "test_phase_id" => "1"), //Pre-Analytical
            array("id" => "3", "name" => "started", "test_phase_id" => "2"), //Analytical
            array("id" => "4", "name" => "completed", "test_phase_id" => "3"), //Post-Analytical
            array("id" => "5", "name" => "verified", "test_phase_id" => "3")//Post-Analytical
        );
        foreach ($test_statuses as $test_status) {
            TestStatus::create($test_status);
        }
        $this->command->info('test_statuses seeded');

        /* Specimen Status table */
        $specimen_statuses = array(
            array("id" => "1", "name" => "specimen-not-collected"),
            array("id" => "2", "name" => "specimen-accepted"),
            array("id" => "3", "name" => "specimen-rejected")
        );
        foreach ($specimen_statuses as $specimen_status) {
            SpecimenStatus::create($specimen_status);
        }
        $this->command->info('specimen_statuses seeded');



        /* Rejection Reasons table */
        $rejection_reasons_array = array(
            array("reason" => "Poorly labelled"),
            array("reason" => "Over saturation"),
            array("reason" => "Insufficient Sample"),
            array("reason" => "Scattered"),
            array("reason" => "Clotted Blood"),
            array("reason" => "Two layered spots"),
            array("reason" => "Serum rings"),
            array("reason" => "Scratched"),
            array("reason" => "Haemolysis"),
            array("reason" => "Spots that cannot elute"),
            array("reason" => "Leaking"),
            array("reason" => "Broken Sample Container"),
            array("reason" => "Mismatched sample and form labelling"),
            array("reason" => "Missing Labels on container and tracking form"),
            array("reason" => "Empty Container"),
            array("reason" => "Samples without tracking forms"),
            array("reason" => "Poor transport"),
            array("reason" => "Lipaemic"),
            array("reason" => "Wrong container/Anticoagulant"),
            array("reason" => "Request form without samples"),
            array("reason" => "Missing collection date on specimen / request form."),
            array("reason" => "Name and signature of requester missing"),
            array("reason" => "Mismatched information on request form and specimen container."),
            array("reason" => "Request form contaminated with specimen"),
            array("reason" => "Duplicate specimen received"),
            array("reason" => "Delay between specimen collection and arrival in the laboratory"),
            array("reason" => "Inappropriate specimen packing"),
            array("reason" => "Inappropriate specimen for the test"),
            array("reason" => "Inappropriate test for the clinical condition"),
            array("reason" => "No Label"),
            array("reason" => "Leaking"),
            array("reason" => "No Sample in the Container"),
            array("reason" => "No Request Form"),
            array("reason" => "Missing Information Required"),
        );
        foreach ($rejection_reasons_array as $rejection_reason) {
            $rejection_reasons[] = RejectionReason::create($rejection_reason);
        }
        $this->command->info('rejection_reasons seeded');



        /* Permissions table */
        $permissions = array(
            array("name" => "view_names", "display_name" => "Can view patient names"),
            array("name" => "manage_patients", "display_name" => "Can add patients"),
            array("name" => "receive_external_test", "display_name" => "Can receive test requests"),
            array("name" => "request_test", "display_name" => "Can request new test"),
            array("name" => "accept_test_specimen", "display_name" => "Can accept test specimen"),
            array("name" => "reject_test_specimen", "display_name" => "Can reject test specimen"),
            array("name" => "change_test_specimen", "display_name" => "Can change test specimen"),
            array("name" => "start_test", "display_name" => "Can start tests"),
            array("name" => "enter_test_results", "display_name" => "Can enter tests results"),
            array("name" => "edit_test_results", "display_name" => "Can edit test results"),
            array("name" => "verify_test_results", "display_name" => "Can verify test results"),
            array("name" => "send_results_to_external_system", "display_name" => "Can send test results to external systems"),
            array("name" => "refer_specimens", "display_name" => "Can refer specimens"),
            array("name" => "manage_users", "display_name" => "Can manage users"),
            array("name" => "manage_test_catalog", "display_name" => "Can manage test catalog"),
            array("name" => "manage_lab_configurations", "display_name" => "Can manage lab configurations"),
            array("name" => "view_reports", "display_name" => "Can view reports"),
            array("name" => "manage_inventory", "display_name" => "Can manage inventory"),
            array("name" => "request_topup", "display_name" => "Can request top-up"),
            array("name" => "manage_qc", "display_name" => "Can manage Quality Control"),
            array("name" => "edit_verified_results", "display_name" => "Can edit verified results"),
            array("name" => "view_specimen_details", "display_name" => "Can view specimen details"),
            array("name" => "view_blood_bank", "display_name" => "Can view Blood Bank"),
			 array("name" => "recieve_request", "display_name" => "Can recieve lab requests"),
			 array("name" => "verify_own", "display_name" => "Can verify test performed by self"),
			 array("name" => "view_result", "display_name" => "Can see  lab  requests  and results"),
			 array("name" => "perform_test", "display_name" => "Can report  lab  requests")
        );

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
        $this->command->info('Permissions table seeded');

        /* Roles table */
        $roles = array(
            array("name" => "Superadmin"),
            array("name" => "Technologist"),
            array("name" => "Receptionist")
        );
        foreach ($roles as $role) {
            Role::create($role);
        }
        $this->command->info('Roles table seeded');

        $user1 = User::find(1);
        $role1 = Role::find(1);
        $permissions = Permission::all();

        //Assign all permissions to role administrator
        foreach ($permissions as $permission) {
            $role1->attachPermission($permission);
        }
        //Assign role Administrator to user 1 administrator
        $user1->attachRole($role1);
    }

}
