<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaction — Meridian</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    :root {
      --color-accent: #8C6B3B;
      --color-bg-light: #f8f9fa;
      --color-border: #e9ecef;
      --color-text: #212529;
      --color-text-muted: #6c757d;
    }

    body {
      background: linear-gradient(135deg, #f5f5f5 0%, #fafafa 100%);
      min-height: 100vh;
      padding: 2rem 1rem;
    }

    .transaction-container {
      max-width: 600px;
      margin: 0 auto;
    }

    .transaction-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .transaction-header h1 {
      font-family: 'Fraunces', serif;
      font-size: 2rem;
      font-weight: 600;
      color: var(--color-text);
      margin-bottom: 0.5rem;
    }

    .transaction-header p {
      color: var(--color-text-muted);
      margin: 0;
    }

    .transaction-card {
      background: white;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    /* Type Selection Buttons */
    .transaction-types {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .type-button {
      padding: 1.25rem 1rem;
      border: 2px solid var(--color-border);
      border-radius: 10px;
      background: white;
      cursor: pointer;
      transition: all 0.3s ease;
      text-align: center;
      font-weight: 500;
      color: var(--color-text-muted);
    }

    .type-button:hover {
      border-color: var(--color-accent);
      background: rgba(140, 107, 59, 0.05);
    }

    .type-button.active {
      border-color: var(--color-accent);
      background: var(--color-accent);
      color: white;
    }

    .type-button i {
      display: block;
      font-size: 1.75rem;
      margin-bottom: 0.5rem;
    }

    .type-button span {
      display: block;
      font-size: 0.95rem;
    }

    /* Divider */
    .divider {
      margin: 1.5rem 0;
      border: none;
      border-top: 1px solid var(--color-border);
    }

    /* Form Sections */
    .form-section {
      display: none;
      animation: fadeIn 0.3s ease;
    }

    .form-section.active {
      display: block;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Form Labels */
    .form-label {
      font-weight: 500;
      color: var(--color-text);
      margin-bottom: 0.5rem;
      font-size: 0.95rem;
    }

    /* Input Groups */
    .input-group {
      margin-bottom: 1.25rem;
    }

    .input-group-text {
      background: var(--color-bg-light);
      border: 1px solid var(--color-border);
      border-right: none;
      color: var(--color-text-muted);
    }

    .form-control {
      border: 1px solid var(--color-border);
      padding: 0.75rem 1rem;
      font-size: 1rem;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--color-accent);
      box-shadow: 0 0 0 0.2rem rgba(140, 107, 59, 0.15);
    }

    /* Info Box */
    .info-box {
      background: rgba(140, 107, 59, 0.05);
      border-left: 4px solid var(--color-accent);
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
    }

    .info-box-label {
      font-size: 0.85rem;
      color: var(--color-text-muted);
      margin-bottom: 0.25rem;
    }

    .info-box-value {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--color-accent);
      font-family: 'IBM Plex Mono', monospace;
    }

    /* Transaction Fees */
    .fee-details {
      background: var(--color-bg-light);
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1.5rem;
      border: 1px solid var(--color-border);
    }

    .fee-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.75rem;
      font-size: 0.95rem;
    }

    .fee-row:last-child {
      margin-bottom: 0;
      padding-top: 0.75rem;
      border-top: 1px solid var(--color-border);
      font-weight: 600;
      font-size: 1rem;
    }

    .fee-label {
      color: var(--color-text-muted);
    }

    .fee-value {
      color: var(--color-text);
      font-family: 'IBM Plex Mono', monospace;
    }

    /* Buttons */
    .btn-accent {
      background: var(--color-accent);
      border: none;
      color: white;
      padding: 0.75rem 1rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-accent:hover {
      background: #6d5328;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(140, 107, 59, 0.3);
    }

    .btn-secondary {
      background: var(--color-bg-light);
      border: 1px solid var(--color-border);
      color: var(--color-text);
      padding: 0.75rem 1rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background: var(--color-border);
      color: var(--color-text);
    }

    /* Button Group */
    .button-group {
      display: flex;
      gap: 1rem;
      margin-top: 2rem;
    }

    .button-group .btn {
      flex: 1;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 500;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    /* Solde Section */
    .solde-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      padding: 1rem;
      background: var(--color-bg-light);
      border-radius: 8px;
    }

    .solde-label {
      color: var(--color-text-muted);
      font-size: 0.9rem;
    }

    .solde-amount {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--color-accent);
      font-family: 'IBM Plex Mono', monospace;
    }

    /* Responsive */
    @media (max-width: 576px) {
      .transaction-card {
        padding: 1.5rem;
      }

      .transaction-types {
        grid-template-columns: 1fr;
      }

      .type-button {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
      }

      .type-button i {
        margin-bottom: 0;
      }

      .type-button span {
        text-align: left;
      }

      .button-group {
        flex-direction: column;
      }
    }

    /* Success/Error Messages */
    .alert {
      border-radius: 8px;
      margin-bottom: 1.5rem;
      border: none;
      animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>

  <div class="transaction-container">

    <!-- Header -->
    <div class="transaction-header">
      <h1>Effectuer une Transaction</h1>
      <p>Gérez vos mouvements financiers en toute sécurité</p>
    </div>


    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
      </div>
    <?php endif; ?>


    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>


    <!-- Main Card -->
    <div class="transaction-card">


      <!-- Solde -->
      <div class="solde-section">
        <div>
          <div class="solde-label">Solde disponible</div>

          <div class="solde-amount">
            <?= number_format($solde ?? 0, 0, ',', ' ') ?> Ar
          </div>

        </div>

        <i class="bi bi-wallet2"
          style="font-size:2rem;color:var(--color-accent);opacity:0.3;">
        </i>

      </div>



      <!-- Types -->
      <div class="transaction-types">

        <button type="button" class="type-button active" data-type="retrait">
          <i class="bi bi-cash-coin"></i>
          <span>Retrait</span>
        </button>


        <button type="button" class="type-button" data-type="depot">
          <i class="bi bi-plus-square"></i>
          <span>Dépôt</span>
        </button>


        <button type="button" class="type-button" data-type="transfert">
          <i class="bi bi-arrow-left-right"></i>
          <span>Transfert</span>
        </button>

      </div>


      <hr class="divider">



      <!-- ================= RETRAIT ================= -->

      <form
        class="form-section active"
        id="retraitForm"
        action="<?= base_url('client/retirer/save') ?>"
        method="post">


        <?= csrf_field() ?>


        <div class="mb-3">

          <label class="form-label">
            Montant à retirer
          </label>


          <div class="input-group">

            <span class="input-group-text">
              <i class="bi bi-currency-exchange"></i>
            </span>


            <input
              type="number"
              class="form-control"
              name="montant"
              placeholder="Entrez le montant"
              min="100"
              required>


            <span class="input-group-text">
              Ar
            </span>


          </div>


          <small class="text-muted">
            Montant minimum : 100 Ar
          </small>


        </div>



        <div class="button-group">

          <button type="submit" class="btn btn-accent">
            <i class="bi bi-check-circle me-2"></i>
            Confirmer le retrait
          </button>


          <button type="reset" class="btn btn-secondary">
            <i class="bi bi-arrow-counterclockwise me-2"></i>
            Annuler
          </button>


        </div>


      </form>






      <!-- ================= DEPOT ================= -->

      <form
        class="form-section"
        id="depotForm"
        action="<?= base_url('client/deposer/save') ?>"
        method="post">


        <?= csrf_field() ?>


        <div class="mb-3">

          <label class="form-label">
            Montant à déposer
          </label>


          <div class="input-group">


            <span class="input-group-text">
              <i class="bi bi-currency-exchange"></i>
            </span>


            <input
              type="number"
              class="form-control"
              name="montant"
              placeholder="Entrez le montant"
              min="100"
              required>


            <span class="input-group-text">
              Ar
            </span>


          </div>


          <small class="text-muted">
            Montant minimum : 100 Ar
          </small>


        </div>




        <div class="button-group">

          <button type="submit" class="btn btn-accent">

            <i class="bi bi-check-circle me-2"></i>
            Confirmer le dépôt

          </button>


          <button type="reset" class="btn btn-secondary">

            <i class="bi bi-arrow-counterclockwise me-2"></i>
            Annuler

          </button>


        </div>


      </form>






      <!-- ================= TRANSFERT ================= -->


      <form
        class="form-section"
        id="transfertForm"
        action="<?= base_url('client/transferer/save') ?>"
        method="post">


        <?= csrf_field() ?>



        <div class="mb-3">


          <label class="form-label">
            Numéro du compte destinataire
          </label>



          <div class="input-group">


            <span class="input-group-text">
              <i class="bi bi-person-circle"></i>
            </span>



            <input
              type="text"
              class="form-control"
              name="numero"
              placeholder="Entrez le numéro client"
              required>


          </div>


        </div>





        <div class="mb-3">


          <label class="form-label">
            Montant à transférer
          </label>



          <div class="input-group">


            <span class="input-group-text">
              <i class="bi bi-currency-exchange"></i>
            </span>


            <input
              type="number"
              class="form-control"
              name="montant"
              id="montant-transfert"
              placeholder="Entrez le montant"
              min="100"
              required>


            <span class="input-group-text">
              Ar
            </span>


          </div>


        </div>





        <!-- Frais -->

        <div class="fee-details">


          <div class="fee-row">
            <span class="fee-label">
              Montant :
            </span>

            <span class="fee-value" id="feeAmount">
              0 Ar
            </span>
          </div>



          <div class="fee-row">

            <span class="fee-label">
              Frais :
            </span>

            <span class="fee-value" id="feeFee">
              0 Ar
            </span>

          </div>




          <div class="fee-row">

            <span class="fee-label">
              Total à débiter :
            </span>

            <span class="fee-value" id="feeTotal">
              0 Ar
            </span>


          </div>



        </div>





        <div class="button-group">


          <button type="submit" class="btn btn-accent">

            <i class="bi bi-check-circle me-2"></i>
            Confirmer le transfert

          </button>




          <button type="reset" class="btn btn-secondary">

            <i class="bi bi-arrow-counterclockwise me-2"></i>
            Annuler

          </button>



        </div>



      </form>


    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ============================
    // Type Selection
    // ============================

    const typeButtons = document.querySelectorAll('.type-button');
    const formSections = document.querySelectorAll('.form-section');


    typeButtons.forEach(button => {

      button.addEventListener('click', function() {

        const type = this.getAttribute('data-type');


        // bouton actif
        typeButtons.forEach(btn => {
          btn.classList.remove('active');
        });

        this.classList.add('active');


        // formulaire actif
        formSections.forEach(form => {
          form.classList.remove('active');
        });


        document
          .getElementById(type + 'Form')
          .classList.add('active');

      });

    });




    // ============================
    // Calcul frais transfert
    // ============================


    const montantTransfertInput =
      document.getElementById('montant-transfert');


    const feeAmountSpan =
      document.getElementById('feeAmount');


    const feeFeeSpan =
      document.getElementById('feeFee');


    const feeTotalSpan =
      document.getElementById('feeTotal');




    function updateFees() {


      const montant =
        parseFloat(montantTransfertInput.value) || 0;


      const frais =
        montant * 0.02;


      const total =
        montant + frais;



      feeAmountSpan.textContent =
        montant.toLocaleString('fr-FR') + ' Ar';



      feeFeeSpan.textContent =
        frais.toLocaleString('fr-FR', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }) + ' Ar';




      feeTotalSpan.textContent =
        total.toLocaleString('fr-FR', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }) + ' Ar';


    }




    if (montantTransfertInput) {

      montantTransfertInput.addEventListener(
        'input',
        updateFees
      );

    }
  </script>
</body>

</html>