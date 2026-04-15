<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — Inmate Registration</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="registration.css">
    <style>
        /* Ensuring select consistency with your existing form design */
        select.crime-select, .unit-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            background: #fff;
            font-size: 13px;
        }
        optgroup {
            font-weight: bold;
            background: #f0f0f0;
        }
        .restriction-note {
            color: #d32f2f;
            font-size: 11px;
            font-weight: bold;
            display: block;
            margin-top: 4px;
        }
        /* New styles for the flexible sentence input */
        .sentence-input-group {
            display: flex;
            gap: 5px;
        }
        .sentence-input-group input {
            flex: 2;
        }
        .sentence-input-group select {
            flex: 1;
        }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — King'ong'o Inmate Management System</div>
        <div class="user-info">Records Officer: Warden Mwangi | <a href="logout.php">[Logout]</a></div>
    </header>

    <nav class="breadcrumb">Home > Inmate Registration</nav>

    <div class="main-container">
        <?php include 'sidebar.php'; ?>

        <main class="content">
            <h2>New Inmate Registration</h2>
            <hr>
            
            <form action="save_inmate.php" method="POST" enctype="multipart/form-data" id="registrationForm">
                
                <section class="registration-form">
                    <div class="form-section">
                        <h3>1. Personal Information & Mugshot</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>FULL NAME</label>
                                <input type="text" name="full_name" placeholder="First Middle Last" required>
                            </div>

                            <div class="form-group">
                                <label>NATIONAL ID</label>
                                <input type="text" 
                                       name="id_number" 
                                       maxlength="9" 
                                       pattern="\d{1,9}" 
                                       title="National ID must be numeric and not exceed 9 digits" 
                                       placeholder="Max 9 digits" 
                                       required>
                                <span class="restriction-note">* Maximum 9 digits allowed.</span>
                            </div>

                            <div class="form-group">
                                <label>DATE OF BIRTH</label>
                                <?php $adult_cutoff = date('Y-m-d', strtotime('-18 years')); ?>
                                <input type="date" name="dob" id="dobInput" max="<?php echo $adult_cutoff; ?>" required>
                                <span class="restriction-note">* Adult facility: Must be 18+ years.</span>
                            </div>
                            <div class="form-group">
                                <label>GENDER</label>
                                <select name="gender">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="form-group" style="grid-column: span 2;">
                                <label>MUGSHOT PHOTO (JPG/PNG)</label>
                                <input type="file" name="inmate_photo" accept="image/*" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section" style="margin-top:30px;">
                        <h3>2. Admission & Sentence</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>OFFENCE (LEGAL CATEGORY)</label>
                                <select name="offence" class="crime-select" required>
                                    <option value="">-- Select Verified Offence --</option>
                                    
                                    <optgroup label="Offences Against Persons">
                                        <option value="Murder">Murder</option>
                                        <option value="Attempted Murder">Attempted Murder</option>
                                        <option value="Manslaughter">Manslaughter</option>
                                        <option value="Assault causing GH">Assault causing Grievous Harm</option>
                                        <option value="Common Assault">Common Assault</option>
                                        <option value="Threatening Violence">Threatening Violence</option>
                                        <option value="Kidnapping / Abduction">Kidnapping / Abduction</option>
                                        <option value="Affray">Affray (Public Fighting)</option>
                                    </optgroup>

                                    <optgroup label="Sexual Offences">
                                        <option value="Defilement">Defilement</option>
                                        <option value="Rape">Rape</option>
                                        <option value="Attempted Rape">Attempted Rape</option>
                                        <option value="Sexual Assault">Sexual Assault</option>
                                        <option value="Indecent Act">Indecent Act</option>
                                        <option value="Incest">Incest</option>
                                        <option value="Sodomy">Unnatural Offences (Sodomy)</option>
                                    </optgroup>

                                    <optgroup label="Offences Against Property">
                                        <option value="Robbery with Violence">Robbery with Violence</option>
                                        <option value="Simple Robbery">Simple Robbery</option>
                                        <option value="Burglary">Burglary (Night)</option>
                                        <option value="House Breaking">House Breaking (Day)</option>
                                        <option value="Theft of Motor Vehicle">Theft of Motor Vehicle</option>
                                        <option value="Stealing by Servant">Stealing by Servant</option>
                                        <option value="General Stealing">General Stealing</option>
                                        <option value="Stock Theft">Stock Theft (Cattle)</option>
                                        <option value="Handling Stolen Goods">Handling Stolen Property</option>
                                        <option value="Arson">Arson (Setting Fire)</option>
                                        <option value="Malicious Damage">Malicious Damage to Property</option>
                                    </optgroup>

                                    <optgroup label="Economic & Fraud Offences">
                                        <option value="Obtaining by False Pretences">Obtaining by False Pretences</option>
                                        <option value="Forgery">Forgery</option>
                                        <option value="Uttering False Documents">Uttering False Documents</option>
                                        <option value="Money Laundering">Money Laundering</option>
                                        <option value="Conspiracy to Defraud">Conspiracy to Defraud</option>
                                        <option value="Cyber Crime">Cyber Crime Act</option>
                                    </optgroup>

                                    <optgroup label="Drug & Narcotics Offences">
                                        <option value="Trafficking Narcotics">Trafficking in Narcotics</option>
                                        <option value="Possession of Narcotics">Possession of Narcotics</option>
                                        <option value="Cultivation of Bhang">Cultivation of Forbidden Plants</option>
                                    </optgroup>

                                    <optgroup label="Public Order & State">
                                        <option value="Treason">Treason</option>
                                        <option value="Incitement to Violence">Incitement to Violence</option>
                                        <option value="Terrorism">Terrorism Related</option>
                                        <option value="Escaping from Custody">Escaping Lawful Custody</option>
                                        <option value="Bribery">Bribery / Corruption</option>
                                    </optgroup>

                                    <optgroup label="Miscellaneous">
                                        <option value="Possession of Firearm">Illegal Possession of Firearm</option>
                                        <option value="Wildlife Crimes">Poaching / Wildlife Crimes</option>
                                        <option value="Other Felony">Other Felony</option>
                                        <option value="Other Misdemeanor">Other Misdemeanor</option>
                                    </optgroup>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>SENTENCE DURATION</label>
                                <div class="sentence-input-group">
                                    <input type="number" name="sentence_value" min="1" placeholder="Amount" required>
                                    <select name="sentence_unit" class="unit-select">
                                        <option value="years">Years</option>
                                        <option value="months">Months</option>
                                        <option value="days">Days</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>COURT OF COMMITTAL</label>
                                <input type="text" name="court_name" placeholder="e.g. Nyeri Law Courts">
                            </div>
                            <div class="form-group">
                                <label>ADMISSION DATE</label>
                                <input type="date" name="admission_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">[ REGISTER INMATE ]</button>
                        <button type="reset" class="btn-secondary">[ Clear Form ]</button>
                    </div>
                </section>
                
            </form>
        </main>
    </div>

    <script>
        // JavaScript validation for the DOB field
        document.getElementById('dobInput').addEventListener('change', function() {
            const dob = new Date(this.value);
            const today = new Date();
            
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }

            if (age < 18) {
                alert("REGISTRATION BLOCKED: This inmate is " + age + " years old. KIMS is an adult correctional facility.");
                this.value = ''; 
            }
        });
    </script>
    <script src="navigation.js"></script>
</body>
</html>