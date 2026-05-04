<form action="proses_permintaan.php" method="POST">
   <label for="id_barang">Pilih Barang:</label>
   <select name="id_barang" id="id_barang">
       <?php
       // Menampilkan data barang yang tersedia dari database
       $query = "SELECT id_barang, nama_barang FROM barang WHERE stok > 0";
       $result = mysqli_query($conn, $query);
       while ($row = mysqli_fetch_assoc($result)) {
           echo "<option value='".$row['id_barang']."'>".$row['nama_barang']."</option>";
       }
       ?>
   </select>

   <label for="jumlah_barang">Jumlah Barang:</label>
   <input type="number" name="jumlah_barang" required>

   <label for="keterangan">Keterangan:</label>
   <textarea name="keterangan"></textarea>

   <input type="submit" value="Ajukan Permintaan">
</form>
