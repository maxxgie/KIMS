<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — Inmate Registration</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="registration.css">
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
            
            <form action="save_inmate.php" method="POST" enctype="multipart/form-data">
                
                <section class="registration-form">
                    <div class="form-section">
                        <h3>1. Personal Information & Mugshot</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>FULL NAME</label>
                                <input type="text" name="full_name" placeholder="First Middle Last" required>
                            </div>
                            <div class="form-group">
                                <label>NATIONAL ID / ALIAS</label>
                                <input type="text" name="id_number">
                            </div>
                            <div class="form-group">
                                <label>DATE OF BIRTH</label>
                                <input type="date" name="dob">
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
                                <label>OFFENCE</label>
                                <input type="text" name="offence" placeholder="e.g. Robbery with Violence" required>
                            </div>
                            <div class="form-group">
                                <label>SENTENCE (YEARS)</label>
                                <input type="number" name="sentence_years" min="0" required>
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

    <script src="navigation.js"></script>
</body>
</html>