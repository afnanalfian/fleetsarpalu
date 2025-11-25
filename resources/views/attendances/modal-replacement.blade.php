<style>
    /* BACKDROP */
    #replacementModal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        backdrop-filter: blur(2px);
        z-index: 99999;
        justify-content: center;
        align-items: center;
    }

    /* MODAL BOX */
    #replacementModal .modal-box {
        background: white;
        width: 600px;
        max-width: 90%;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        animation: fadeInScale .2s ease-out;
    }

    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.9); }
        to   { opacity: 1; transform: scale(1); }
    }

    .modal-header-custom {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .close-btn {
        background: #dc3545;
        border: none;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        cursor: pointer;
    }

    .replacement-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .replacement-item:hover {
        background: #f8f9fa;
    }
</style>

<!-- =========================
     CUSTOM MODAL
========================= -->
<div id="replacementModal">
    <div class="modal-box">

        <div class="modal-header-custom mb-3">
            <h5>Pilih Pengganti</h5>
            <button class="close-btn" onclick="closeReplacementModal()">X</button>
        </div>

        <input type="text" id="searchUser" class="form-control mb-3" placeholder="Cari nama...">

        <div id="replacementList" style="max-height: 350px; overflow-y: auto;"></div>

    </div>
</div>

<!-- =========================
     JAVASCRIPT MODAL
========================= -->
<script>
    let selectedMember = null;

    // OPEN MODAL
    function openReplacementModal(memberId) {
        selectedMember = memberId;
        loadReplacementList("");
        document.getElementById("replacementModal").style.display = "flex";
        document.getElementById("searchUser").value = "";
        document.body.style.overflow = "hidden"; // prevent scroll
    }

    // CLOSE MODAL
    function closeReplacementModal() {
        document.getElementById("replacementModal").style.display = "none";
        document.body.style.overflow = "auto";
    }

    // Close modal if clicking on backdrop
    document.getElementById("replacementModal").addEventListener("click", function(e) {
        if (e.target === this) {
            closeReplacementModal();
        }
    });

    // SEARCH LIVE
    document.addEventListener("keyup", function(e){
        if(e.target.id === "searchUser"){
            loadReplacementList(e.target.value);
        }
    });

    // AJAX LOAD LIST
    function loadReplacementList(keyword) {
        fetch("{{ route('attendances.searchReplacement') }}?q=" + keyword + "&check_id={{ $checking->id }}")
            .then(res => res.text())
            .then(html => {
                document.getElementById("replacementList").innerHTML = html;

                // BIND BUTTONS
                document.querySelectorAll(".choose-replacement").forEach(btn => {
                    btn.onclick = function () {
                        let id = this.dataset.id;
                        let name = this.dataset.name;

                        // CEK DUPLIKASI
                        let allReplacements = document.querySelectorAll('input[id^="replacement_"]');
                        for (let rep of allReplacements) {
                            if (rep.id !== ("replacement_" + selectedMember) && rep.value == id) {
                                alert("Pengganti ini sudah dipilih untuk anggota lain!");
                                return;
                            }
                        }

                        // isi hidden input
                        document.getElementById('replacement_' + selectedMember).value = id;

                        // tampilkan nama
                        document.getElementById('replacement_name_' + selectedMember).innerHTML =
                            "<strong>" + name + "</strong>";

                        // tampilkan tombol hapus
                        const removeBtn = document.querySelector(`[data-member="${selectedMember}"].remove-replacement`);
                        if (removeBtn) {
                            removeBtn.classList.remove('d-none');
                        }

                        closeReplacementModal();
                    };
                });
            });
    }
</script>
