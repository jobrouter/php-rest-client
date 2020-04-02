.. include:: ../Includes.txt

.. _api-incident:

===============
Model\\Incident
===============

.. php:class:: final Brotkrueml\\JobRouterClient\\Model\\Incident

   Incident model class which collects the fields for a process instance start.

   .. php:const:: const PRIORITY_HIGH = 3

      The value of the high priority.

   .. php:const:: const PRIORITY_LOW = 1

      The value of the low priority.

   .. php:const:: const PRIORITY_NORMAL = 2

      The value of the default priority.

   .. php:method:: addRowToSubTable($subTableName, $row)

      Add a row to a sub table.

      :param string $subTableName: The name of the sub table.
      :param array $row: The row for the sub table.
      :returns self: An instance of itself.

   .. php:method:: getIncidentEscalationDate()

      Retrieve the incident escalation date.

      :returns ?\\DateTimeInterface: The date of the incident escalation, or :php:`null`, if not defined.

   .. php:method:: getInitiator()

      Retrieve the initiator.

      :returns string: The initiator.

   .. php:method:: getJobfunction()

      Retrieve the Job Function.

      :returns string: The Job Function.

   .. php:method:: getPool()

      Retrieve the pool number, or :php:`null` if not defined.

      :returns ?int: The pool number.

   .. php:method:: getPriority()

      Retrieve the priority, or :php:`null` if not defined.

      :returns ?int: The priority.

   .. php:method:: getProcessTableField($name)

      Retrieve the value of a process table field.

      :param string $name: The name of the process table field
      :returns string|int|bool|Brotkrueml\\JobRouterClient\\Resource\\FileInterface|null: The value of a process table field, or :php:`null`, if not existing.

   .. php:method:: getRowsForSubTable($subTableName)

      Retrieve the rows for the given sub table.

      :param string $subTableName: The name of the sub table.
      :returns ?array: The rows of the sub table, or :php:`null`, if not existing.

   .. php:method:: getStep()

      Retrieve the step number.

      :returns ?int: The step number, or :php:`null` if not defined.

   .. php:method:: getStepEscalationDate()

      Retrieve the step escalation date.

      :returns ?\\DateTimeInterface: The date of the step escalation, or :php:`null`, if not defined.

   .. php:method:: getSummary()

      Retrieve the summary.

      :returns string: The summary.

   .. php:method:: getUsername()

      Retrieve the username.

      :returns string: The username.

   .. php:method:: isSimulation()

      It is a simulation incident.

      :returns bool: :php:`true`, if simulation, otherwise :php:`false`.

   .. php:method:: setIncidentEscalationDate($incidentEscalationDate)

      Sets the incident escalation date.

      :param \\DateTimeInterface $incidentEscalationDate: The incident escalation date.
      :returns self: An instance of itself.

   .. php:method:: setInitiator($initiator)

      Sets the initiator.

      :param string $initiator: The initiator.
      :returns self: An instance of itself.

   .. php:method:: setJobfunction($jobfunction)

      Sets the Job Function.

      :param string $jobfunction: The Job Function.
      :returns self: An instance of itself.

   .. php:method:: setPool($pool)

      Sets the pool number (must be a positive integer).

      :param int $pool: The pool number.
      :returns self: An instance of itself.

   .. php:method:: setPriority($priority)

      Sets the priority (values 1-3 are allowed, you can use the PRIORITY
      constants defined in this class).

      :param int $priority: The priority.
      :returns self: An instance of itself.

   .. php:method:: setProcessTableField($name, $value)

      Sets the value of a process table field.

      :param string $name: The name of the process table field.
      :param string|int|bool|Brotkrueml\\JobRouterClient\\Resource\\FileInterface $value: The value of the process table field.
      :returns self: An instance of itself.

   .. php:method:: setRowsForSubTable($subTableName, $rows)

      Set the rows for the given sub table

      :param string $subTableName: The name of the sub table.
      :param array $rows: The rows for the sub table.
      :returns self: An instance of itself.

   .. php:method:: setSimulation($simulation)

      Sets the simulation.

      :param bool $simulation: :php:`true`, if simulation, otherwise :php:`false`.
      :returns self: An instance of itself.

   .. php:method:: setStep($step)

      Sets the step number.

      :param int $step: The step number.
      :returns self: An instance of itself.

   .. php:method:: setStepEscalationDate($stepEscalationDate)

      Sets the step escalation date.

      :param \\DateTimeInterface $stepEscalationDate: The step escalation date.
      :returns self: An instance of itself.

   .. php:method:: setSummary($summary)

      Sets the summary.

      :param string $username: The summary.
      :returns self: An instance of itself.

   .. php:method:: setUsername($username)

      Sets the username.

      :param string $username: The username.
      :returns self: An instance of itself.


Usage Example
-------------

::

   <?php
   use Brotkrueml\JobRouterClient\Model\Incident;
   use Brotkrueml\JobRouterClient\Resource\File;

   require_once 'vendor/autoload.php';

   $incident = (new Incident())
      ->setStep(1)
      ->setSummary('Instance started with IncidentsClient')
      ->setPriority(Incident::PRIORITY_HIGH)
      ->setUsername('johndoe')
      ->setProcessTableField('invoicenumber', 'in42')
      ->setProcessTableField(
         'invoicefile',
         new File('/path/to/invoice/in42.jpg')
      )
      ->setRowsForSubtable(
         'positions',
         [
            [
               'description' => 'invoice position #1',
               'price' => '12.34',
            ],
            [
               'description' => 'invoice position #2',
               'price' => '23.45',
            ],
         ]
      )
      ->addRowToSubtable(
         'positions',
         [
               'description' => 'invoice position #3',
               'price' => '54.32',
         ]
      )
   ;
