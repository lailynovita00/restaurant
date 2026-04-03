
        <!-- Logout Confirmation Modal -->
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoutModalLabel"><x-bi en="Confirm Logout" ar="تأكيد تسجيل الخروج" /></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <x-bi en="Are you sure you want to log out now?" ar="هل أنت متأكد إنك عايز تسجّل خروج دلوقتي؟" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><x-bi en="Cancel" ar="إلغاء" /></button>
                        <form id="logout-form" action="{{ route('auth.logout') }}" method="GET" class="d-inline">
                            <button type="submit" class="btn btn-danger"><x-bi en="Logout" ar="تسجيل الخروج" /></button>
                        </form>
                    </div>
                </div>
            </div>
          </div>
  
