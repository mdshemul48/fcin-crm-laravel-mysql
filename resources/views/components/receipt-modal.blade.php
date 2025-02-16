<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptModalLabel">Receipt Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="receiptImage" class="img-fluid" alt="Receipt">
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function showReceipt(imageUrl) {
            document.getElementById('receiptImage').src = imageUrl;
            new bootstrap.Modal(document.getElementById('receiptModal')).show();
        }
    </script>
@endpush
