CREATE VIEW EmployeeRequests AS
SELECT
    'Leave' AS RequestType,
    LeaveID AS RequestID,
    EmployeeID,
    LeaveDate AS StartDate,
    LeaveDate AS EndDate,
    HoursRequested AS Duration,
    Reason,
    ApprovedBy,
    RequestDate,
    IsApproved
FROM
    LeaveRequests
UNION ALL
SELECT
    'Vacation' AS RequestType,
    RequestID,
    EmployeeID,
    StartDate,
    EndDate,
    DaysRequested AS Duration,
    Reason,
    ApprovedBy,
    RequestDate,
    IsApproved
FROM
    VacationRequests;
