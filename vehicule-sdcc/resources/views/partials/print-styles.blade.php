<style>
    /* Shared print styles for all pages (Nouvelle Demande style) */
    @page { size: A4; margin: 10mm; }

    @media print {
        .no-print { display: none !important; }

        html, body { background: #fff !important; color: #000 !important; font-size: 11px; }

        /* Hide chrome */
        .top-navbar, .sidebar, .sidebar-overlay, .profile-dropdown, .notifications-dropdown, .back-link, .btn-print, .btn, .form-actions, .filters, .actions, .table-actions { display: none !important; }

        /* Main content full width */
        .main-content, .content-wrapper, .content, .container { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }

        /* Print header and footer (layout provides .print-header/.print-footer) */
        .print-header { display: block; text-align: left; padding: 8px 0; border-bottom: 1px solid #000; margin-bottom: 8px; }
        .print-header .print-title, .print-title { font-size: 16px; font-weight: 700; color: #000; }
        .print-header .print-date, .print-date { font-size: 12px; color: #222; }

        .print-footer { display: block; position: fixed; bottom: 0; left: 0; right: 0; padding: 6px 12px; border-top: 1px solid #000; background: #fff; text-align: right; font-size: 12px; }

        /* Tables */
        table, th, td { border: 1px solid #000 !important; }
        table { width: 100% !important; border-collapse: collapse !important; page-break-inside: auto; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        th, td { padding: 6px 8px !important; font-size: 12px; color: #000 !important; }

        /* Cards and forms to appear as simple blocks */
        .request-card, .print-sheet, .print-summary, .form-section { background: none !important; box-shadow: none !important; border: 1px solid #000 !important; padding: 8px !important; margin-bottom: 8px !important; }

        /* Ensure images are printable and not too large */
        img { max-width: 120px !important; height: auto !important; }

        /* Page number counter (uses .page-number element) */
        .page-number:after { content: "Page " counter(page); }

        @page { counter-increment: page; }
    }

</style>
