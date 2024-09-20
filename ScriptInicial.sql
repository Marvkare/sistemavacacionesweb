CREATE TABLE Employees (
    EmployeeID INT PRIMARY KEY AUTO_INCREMENT,
    FirstName VARCHAR(50),
    LastName VARCHAR(50),
    HireDate DATE,
    LastVacationUpdate DATE,
    HoursRequested INT,
    CurrentEntitlement INT,
    DaysTaken INT DEFAULT 0,
    RFC VARCHAR(13) NOT NULL,
    IsActive TINYINT(1) DEFAULT 1,
    Departament VARCHAR(30) NOT NULL
);


CREATE TABLE Admins (
    AdminID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL -- Considera almacenar contraseñas de manera segura (hash)
);

INSERT INTO Admins (AdminID,Username,Password) VALUES(1,"adminP","$2y$10$mNSoLsVMN1QVevVu1cJv7ug52v1QY/jgAf1C/6LqRHdND5LasWhwC");

CREATE TABLE VacationEntitlement (
    YearsWorked INT PRIMARY KEY,
    VacationDays INT
);

-- Insertar los días de vacaciones correspondientes a los años trabajados
INSERT INTO VacationEntitlement (YearsWorked, VacationDays) VALUES
(1, 12),
(2, 14),
(3, 16),
(4, 18),
(5, 20),
(6, 22),
(11, 24),
(16, 26),
(21, 28),
(26, 30),
(31, 32);



-- Tabla para solicitudes de permisos parciales
CREATE TABLE LeaveRequests (
    LeaveID INT PRIMARY KEY AUTO_INCREMENT,
    EmployeeID INT,
    LeaveDate DATE NOT NULL,              -- Fecha específica del permiso
    DepartureDateTime DATETIME NOT NULL,  -- Fecha y hora de salida
    ReturnDateTime DATETIME NOT NULL,     -- Fecha y hora de regreso
    HoursRequested INT,                   -- Horas solicitadas
    ApprovedBy INT,
    RequestDate DATETIME,
    IsApproved TINYINT(1) DEFAULT 0,      -- Columna para estado de aprobación
    Reason VARCHAR(100),
    IsCounted TINYINT(1) DEFAULT 0,        -- Columna para el conteo 
    FOREIGN KEY (EmployeeID) REFERENCES Employees(EmployeeID)
);


-- Tabla para solicitudes de vacaciones completas
CREATE TABLE VacationRequests (
    RequestID INT PRIMARY KEY AUTO_INCREMENT,
    EmployeeID INT,
    StartDate DATE NOT NULL,              -- Fecha de inicio de las vacaciones
    EndDate DATE NOT NULL,                -- Fecha de fin de las vacaciones
    DaysRequested INT,
    Reason VARCHAR(100),
    ApprovedBy INT,
    RequestDate DATETIME,
    IsApproved BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (EmployeeID) REFERENCES Employees(EmployeeID)
);


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
