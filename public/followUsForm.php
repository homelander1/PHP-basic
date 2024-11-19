    <!-- Follow Us modal window -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <div class="followUsTitle">Follow Us<div class="close">X</div>
            </div>
            <form id="contactForm" action="index.php" method="POST">
                <input type="hidden" name="form_type" value="subscribe">
                <label for="name">Name<span class="requiredField">*</span></label>
                <input type="text" id="name" name="name" class="formField" placeholder="John Smith" required>
                <label for="email">Email<span class="requiredField">*</span></label>
                <input type="email" id="email" name="email" class="formField" placeholder="example@example.com" required>
                <button type="submit" class="followUs_subscribe">SUBSCRIBE</button>
            </form>
        </div>
    </div>