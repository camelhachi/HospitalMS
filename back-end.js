const express = require('express');
const app = express();
const port = 3000;

// Middleware to handle JSON responses
app.use(express.json());

// Sample data for the dashboard
let statsData = {
  occupiedRooms: 110,
  availableRooms: 40,
  totalRooms: 150
};

let inpatientStatistics = [
  { id: 1, week: 'Week 1', patients: 60 },
  { id: 2, week: 'Week 2', patients: 80 },
  { id: 3, week: 'Week 3', patients: 100 },
  { id: 4, week: 'Week 4', patients: 120 }
];

let patientList = [
  { id: '1234XYZ', name: 'Chiya Shafira', age: 22, room: 'B12', admDate: '18/08/24', status: 'Discharged' },
  { id: '2311ABC', name: 'Ananda Dewayani', age: 42, room: 'A22', admDate: '20/08/24', status: 'Ongoing' }
];

let visitSchedule = [
  { id: 1, doctor: 'dr. Dina', time: '10.00-11.30', patients: 3 },
  { id: 2, doctor: 'dr. Sarah', time: '10.00-11.30', patients: 5 },
  { id: 3, doctor: 'dr. Haikal', time: '08.00-09.30', patients: 2 },
  { id: 4, doctor: 'dr. Arka', time: '15.00-16.30', patients: 8 }
];

let incidentReports = [
  { id: 1, incident: 'I.V. Change', location: 'A01', time: '13.20' },
  { id: 2, incident: 'I.V. Change', location: 'B04', time: '13.42' }
];

let waitingForRoom = [
  { id: 1, name: 'Maria Pinasinta', location: 'OPD' },
  { id: 2, name: 'Rosa Salsabila', location: 'OPD' },
  { id: 3, name: 'Aqilla Kusuma', location: 'ER' },
  { id: 4, name: 'Zakariya Gambetta', location: 'OPD' }
];

let waitingForDischarge = [
  { id: 1, name: 'Nadiver Feritas', room: 'B02' },
  { id: 2, name: 'Akmal Hidayat', room: 'A02' },
  { id: 3, name: 'Nazla Camilla', room: 'B08' },
  { id: 4, name: 'Janitra Qodiyah', room: 'B05' }
];

// CRUD Routes for the dashboard

// 1. Stats Data CRUD
// GET: Read stats data
app.get('/stats', (req, res) => {
  res.json(statsData);
});

// PUT: Update stats data
app.put('/stats', (req, res) => {
  const { occupiedRooms, availableRooms, totalRooms } = req.body;
  statsData = { occupiedRooms, availableRooms, totalRooms };
  res.json({ message: 'Stats updated successfully', stats: statsData });
});

// 2. Inpatient Statistics CRUD
// GET: Read all inpatient statistics
app.get('/inpatient-statistics', (req, res) => {
  res.json(inpatientStatistics);
});

// POST: Create new inpatient statistic
app.post('/inpatient-statistics', (req, res) => {
  const newStatistic = { id: inpatientStatistics.length + 1, ...req.body };
  inpatientStatistics.push(newStatistic);
  res.status(201).json({ message: 'Statistic added successfully', statistic: newStatistic });
});

// PUT: Update an inpatient statistic
app.put('/inpatient-statistics/:id', (req, res) => {
  const id = parseInt(req.params.id);
  const index = inpatientStatistics.findIndex((stat) => stat.id === id);
  if (index !== -1) {
    inpatientStatistics[index] = { id, ...req.body };
    res.json({ message: 'Statistic updated successfully', statistic: inpatientStatistics[index] });
  } else {
    res.status(404).json({ message: 'Statistic not found' });
  }
});

// DELETE: Delete an inpatient statistic
app.delete('/inpatient-statistics/:id', (req, res) => {
  const id = parseInt(req.params.id);
  inpatientStatistics = inpatientStatistics.filter((stat) => stat.id !== id);
  res.json({ message: 'Statistic deleted successfully' });
});

// 3. Patient List CRUD
// GET: Read all patients
app.get('/patients', (req, res) => {
  res.json(patientList);
});

// POST: Add a new patient
app.post('/patients', (req, res) => {
  const newPatient = { id: `P${patientList.length + 1}`, ...req.body };
  patientList.push(newPatient);
  res.status(201).json({ message: 'Patient added successfully', patient: newPatient });
});

// PUT: Update a patient by ID
app.put('/patients/:id', (req, res) => {
  const id = req.params.id;
  const index = patientList.findIndex((patient) => patient.id === id);
  if (index !== -1) {
    patientList[index] = { id, ...req.body };
    res.json({ message: 'Patient updated successfully', patient: patientList[index] });
  } else {
    res.status(404).json({ message: 'Patient not found' });
  }
});

// DELETE: Remove a patient by ID
app.delete('/patients/:id', (req, res) => {
  const id = req.params.id;
  patientList = patientList.filter((patient) => patient.id !== id);
  res.json({ message: 'Patient deleted successfully' });
});

// 4. Visit Schedule CRUD
// GET: Read visit schedule
app.get('/visit-schedule', (req, res) => {
  res.json(visitSchedule);
});

// POST: Add a new visit schedule
app.post('/visit-schedule', (req, res) => {
  const newSchedule = { id: visitSchedule.length + 1, ...req.body };
  visitSchedule.push(newSchedule);
  res.status(201).json({ message: 'Visit schedule added successfully', schedule: newSchedule });
});

// PUT: Update a visit schedule by ID
app.put('/visit-schedule/:id', (req, res) => {
  const id = parseInt(req.params.id);
  const index = visitSchedule.findIndex((schedule) => schedule.id === id);
  if (index !== -1) {
    visitSchedule[index] = { id, ...req.body };
    res.json({ message: 'Visit schedule updated successfully', schedule: visitSchedule[index] });
  } else {
    res.status(404).json({ message: 'Visit schedule not found' });
  }
});

// DELETE: Remove a visit schedule by ID
app.delete('/visit-schedule/:id', (req, res) => {
  const id = parseInt(req.params.id);
  visitSchedule = visitSchedule.filter((schedule) => schedule.id !== id);
  res.json({ message: 'Visit schedule deleted successfully' });
});

// 5. Incident Reports CRUD
// GET: Read incident reports
app.get('/incident-reports', (req, res) => {
  res.json(incidentReports);
});

// POST: Add a new incident report
app.post('/incident-reports', (req, res) => {
  const newIncident = { id: incidentReports.length + 1, ...req.body };
  incidentReports.push(newIncident);
  res.status(201).json({ message: 'Incident report added successfully', incident: newIncident });
});

// PUT: Update an incident report by ID
app.put('/incident-reports/:id', (req, res) => {
  const id = parseInt(req.params.id);
  const index = incidentReports.findIndex((incident) => incident.id === id);
  if (index !== -1) {
    incidentReports[index] = { id, ...req.body };
    res.json({ message: 'Incident report updated successfully', incident: incidentReports[index] });
  } else {
    res.status(404).json({ message: 'Incident report not found' });
  }
});

// DELETE: Remove an incident report by ID
app.delete('/incident-reports/:id', (req, res) => {
  const id = parseInt(req.params.id);
  incidentReports = incidentReports.filter((incident) => incident.id !== id);
  res.json({ message: 'Incident report deleted successfully' });
});

// 6. Waiting for Room CRUD
// GET: Read all patients waiting for room
app.get('/waiting-for-room', (req, res) => {
  res.json(waitingForRoom);
});

// POST: Add a patient to the waiting-for-room list
app.post('/waiting-for-room', (req, res) => {
  const newWaiting = { id: waitingForRoom.length + 1, ...req.body };
  waitingForRoom.push(newWaiting);
  res.status(201).json({ message: 'Patient added to waiting for room', waiting: newWaiting });
});

// PUT: Update a patient in the waiting-for-room list
app.put('/waiting-for-room/:id', (req, res) => {
  const id = parseInt(req.params.id);
  const index = waitingForRoom.findIndex((waiting) => waiting.id === id);
  if (index !== -1) {
    waitingForRoom[index] = { id, ...req.body };
    res.json({ message: 'Waiting for room updated successfully', waiting: waitingForRoom[index] });
  } else {
    res.status(404).json({ message: 'Patient not found in waiting list' });
  }
});

// DELETE: Remove a patient from the waiting-for-room list
app.delete('/waiting-for-room/:id', (req, res) => {
  const id = parseInt(req.params.id);
  waitingForRoom = waitingForRoom.filter((waiting) => waiting.id !== id);
  res.json({ message: 'Patient removed from waiting for room list' });
});

// 7. Waiting for Discharge CRUD
// GET: Read all patients waiting for discharge
app.get('/waiting-for-discharge', (req, res) => {
  res.json(waitingForDischarge);
});

// POST: Add a patient to the waiting-for-discharge list
app.post('/waiting-for-discharge', (req, res) => {
  const newWaiting = { id: waitingForDischarge.length + 1, ...req.body };
  waitingForDischarge.push(newWaiting);
  res.status(201).json({ message: 'Patient added to waiting for discharge', waiting: newWaiting });
});

// PUT: Update a patient in the waiting-for-discharge list
app.put('/waiting-for-discharge/:id', (req, res) => {
  const id = parseInt(req.params.id);
  const index = waitingForDischarge.findIndex((waiting) => waiting.id === id);
  if (index !== -1) {
    waitingForDischarge[index] = { id, ...req.body };
    res.json({ message: 'Waiting for discharge updated successfully', waiting: waitingForDischarge[index] });
  } else {
    res.status(404).json({ message: 'Patient not found in waiting list' });
  }
});

// DELETE: Remove a patient from the waiting-for-discharge list
app.delete('/waiting-for-discharge/:id', (req, res) => {
  const id = parseInt(req.params.id);
  waitingForDischarge = waitingForDischarge.filter((waiting) => waiting.id !== id);
  res.json({ message: 'Patient removed from waiting for discharge list' });
});

// Start the server
app.listen(port, () => {
  console.log(`Dashboard backend with CRUD running at http://localhost:${port}`);
});
