-- EDS reports
SELECT
  measures.name,
  test_results.result
FROM
  measures
  INNER JOIN test_results ON test_results.measure_id = measures.id
WHERE
  measures.name = 'Parasites' AND
  Date_Format(test_results.time_entered, "%M %Y") = 'September 2018';
  
-- For notifications
SELECT
  Count(DISTINCT tests.id) AS Count_id
FROM
  tests
  INNER JOIN test_types ON tests.test_type_id = test_types.id
  INNER JOIN test_categories ON test_types.test_category_id = test_categories.id
  INNER JOIN ips ON ips.test_category_id = test_categories.id
WHERE
  ips.ip = 'IMMUNO SEROLOGY' AND
  tests.test_status_id = 4 AND
  Date_Format(tests.time_created, "%Y %m %d") = Date_Format(CurDate(), "%Y %m %d")

-- in_client income
SELECT
  visits.department,
  visits.visit_type,
  Count(DISTINCT tests.id) AS Tests,
  Count(DISTINCT visits.id) AS Requests,
  Sum(tests.paid_amount) AS Income
FROM
  visits
  INNER JOIN tests ON tests.visit_id = visits.id
WHERE
  visits.department IN ('IM', 'Dialysis', 'Stomato', 'ARV IM', 'ARV PED', 'Surg', 'Obs gyn', 'Ped', 'ENT/ORL',
  'Surg', 'Ophtalmo', 'ICU','Dermato', 'Emergency') AND
  Date_Format(visits.created_at, "%M %Y") = 'September 2018'
GROUP BY
  visits.department,
  visits.visit_type

-- noncreatedspecimens
SELECT
  tests.specimen_id
FROM
  tests
WHERE
  tests.specimen_id NOT IN (SELECT
      specimens.id
    FROM
      specimens)

-- outclientincome
SELECT
  visits.department,
  visits.visit_type,
  Count(DISTINCT tests.id) AS Tests,
  Count(DISTINCT visits.id) AS Requests,
  Sum(tests.paid_amount) AS Income
FROM
  visits
  INNER JOIN tests ON tests.visit_id = visits.id
WHERE
  visits.department NOT IN ('IM', 'Dialysis', 'Stomato', 'ARV IM', 'ARV PED', 'Surg', 'Obs gyn', 'Ped', 'ENT/ORL',
  'Surg', 'Ophtalmo', 'ICU','Dermato', 'Emergency') AND
  Date_Format(visits.created_at, "%M %Y") = 'September 2018'

GROUP BY
  visits.department,
  visits.visit_type

-- patients
SELECT
  patients.gender,
  Count(patients.id) AS Count_id,
  Avg(DateDiff(patients.created_at, patients.dob) / 365) AS age_av,
  Max(DateDiff(patients.created_at, patients.dob) / 365) AS max_age,
  Min(DateDiff(patients.created_at, patients.dob) / 365) AS min_age
FROM
  patients
WHERE
  Year(patients.dob) BETWEEN 1900 AND 2018 AND
  Date_Format(patients.created_at, "%M %Y") = 'July 2018'
GROUP BY
  patients.gender

-- serviceincome
SELECT
  test_categories.name,
  Count(DISTINCT tests.id) AS test_number,
  Sum(tests.paid_amount) AS income
FROM
  tests
  INNER JOIN test_types ON tests.test_type_id = test_types.id
  INNER JOIN test_categories ON test_types.test_category_id = test_categories.id
WHERE
  Date_Format(tests.time_created, "%M %Y") = 'September 2018'
GROUP BY
  test_categories.name

-- testavgvalue
SELECT
  measures.name,
  Avg(test_results.result) AS Avg_result,
  measures.unit
FROM
  measures
  INNER JOIN test_results ON test_results.measure_id = measures.id
WHERE
  measures.measure_type_id = 1 AND
 Date_Format(test_results.time_entered, "%M %Y") = 'September 2018'
GROUP BY
  measures.name,
  measures.unit

-- test_COMM
SELECT
  test_types.name,
  Count(tests.id) AS test_number,
  Sum(tests.paid_amount) AS test_income
FROM
  tests
  INNER JOIN test_types ON tests.test_type_id = test_types.id
WHERE
  tests.paid_amount = test_types.tarif_D AND
  Date_Format(tests.time_created, "%M %Y") = 'September 2018'
GROUP BY
  test_types.name

-- TEST_INCOME
SELECT
  chub_lab_prod.test_types.name,
  Count(chub_lab_prod.tests.id) AS Count_id,
  Sum(chub_lab_prod.tests.paid_amount) AS Sum_paid_amount
FROM
  chub_lab_prod.tests
  INNER JOIN chub_lab_prod.test_types
    ON chub_lab_prod.tests.test_type_id = chub_lab_prod.test_types.id
WHERE
  Date_Format(tests.time_created, "%M %Y") = 'July 2018'
GROUP BY
  chub_lab_prod.test_types.name

-- test_MMI
SELECT
  test_types.name,
  Count(tests.id) AS test_number,
  Sum(tests.paid_amount) AS test_income
FROM
  tests
  INNER JOIN test_types ON tests.test_type_id = test_types.id
WHERE
  tests.paid_amount = test_types.tarif_B AND
  Date_Format(tests.time_created, "%M %Y") = 'July 2018'
GROUP BY
  test_types.name

-- test_MSC
SELECT
  test_types.name,
  Count(tests.id) AS test_number,
  Sum(tests.paid_amount) AS test_income
FROM
  tests
  INNER JOIN test_types ON tests.test_type_id = test_types.id
WHERE
  tests.paid_amount = test_types.tarif_A AND
  Date_Format(tests.time_created, "%M %Y") = 'September 2018'
GROUP BY
  test_types.name

-- test_PRIV
SELECT
  test_types.name,
  Count(tests.id) AS test_number,
  Sum(tests.paid_amount) AS test_income
FROM
  tests
  INNER JOIN test_types ON tests.test_type_id = test_types.id
WHERE
  tests.paid_amount = test_types.tarif_E AND
  Date_Format(tests.time_created, "%M %Y") = 'July 2018'
GROUP BY
  test_types.name

-- test_RSSB
SELECT
  test_types.name,
  Count(tests.id) AS test_number,
  Sum(tests.paid_amount) AS test_income
FROM
  tests
  INNER JOIN test_types ON tests.test_type_id = test_types.id
WHERE
  tests.paid_amount = test_types.tarif_C AND
  Date_Format(tests.time_created, "%M %Y") = 'July 2018'
GROUP BY
  test_types.name


  
  