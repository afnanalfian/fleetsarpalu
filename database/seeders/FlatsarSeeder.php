<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Team;
use App\Models\Vehicle;

class FlatsarSeeder extends Seeder
{
    public function run(): void
    {
        /* ===========================
         *  Insert Teams
         * =========================== */
        $teamNames = ['ALFA', 'BRAVO', 'CHARLIE', 'DELTA', 'ECHO'];
        $teamIds = [];

        foreach ($teamNames as $name) {
            $team = Team::create([
                'name' => $name,
                'leader_id' => null,
            ]);
            $teamIds[$name] = $team->id;
        }

        /* ===========================
         *  Insert Users
         * =========================== */
        $users = [
            ['ADMIN', null, 'admin@fleetsar.palu', null, 'Admin', null],
            ['AFRISAL SOELAIMAN S. E.', '198508292005021001', 'afrisal.soelaiman@basarnas.go.id', '082199469344', 'Kepala Sumber Daya', null],
            ['DIRMAN SANDEWA S.AP.', '198908172009121001', 'dirman.sandewa@basarnas.go.id', '082189417889', 'Ketua Tim', 'BRAVO'],
            ['ADIANSYAH', '199004122010121001', 'adiansyah@basarnas.go.id', '085240697154', 'Pegawai', 'ECHO'],
            ['RICKY MALLAWAN', '198912292009121001', 'ricky.mallawan@basarnas.go.id', '082139560168', 'Ketua Tim', 'CHARLIE'],
            ['DIMAS TRIATMOJO', '198807232009121001', 'dimas.triatmojo@basarnas.go.id', '0811416664', 'Ketua Tim', 'DELTA'],
            ['SAYUDI YUSUF GINANJAR', '199011282009121001', 'sayudi.ginanjar@basarnas.go.id', '085215705557', 'Ketua Tim', 'ECHO'],
            ['MARIO PASKAHLIS RUBAK ALLO', '198704102009121003', 'mario.allo@basarnas.go.id', '082192414089', 'Ketua Tim', 'ALFA'],
            ['MUHTAR', '199102122010121001', 'muhtar@basarnas.go.id', '082271093503', 'Pegawai', 'ALFA'],
            ['TAKDIR ZULKIFLI', '199104282010121001', 'takdir.zulkifli@basarnas.go.id', '082292003123', 'Pegawai', 'CHARLIE'],
            ['BAHTIAR', '198712282010121003', 'bahtiar@basarnas.go.id', '085242868209', 'Pegawai', 'BRAVO'],
            ['JABBAR', '198904262010121005', 'jabbar@basarnas.go.id', '082333666614', 'Pegawai', 'CHARLIE'],
            ['YULIA SARI PUTRI BRASILIA', '199407182015032001', 'yulia.brasilia@basarnas.go.id', '082257809534', 'Pegawai', 'ALFA'],
            ['ANDI SAFRULLAH SYARIYAMTO', '199009192015031003', 'andi.syariyamto@basarnas.go.id', '085255856826', 'Pegawai', 'CHARLIE'],
            ['RYAN R KATILI', '199512012015031001', 'ryan.katili@basarnas.go.id', '085337274381', 'Pegawai', 'DELTA'],
            ['IMAM TAUFIQ', '199409132015031001', 'imam.taufiq@basarnas.go.id', '082188222298', 'Pegawai', 'ECHO'],
            ['FERAWATI DANI A.Md.', '199211092020122002', 'ferawati.dani@basarnas.go.id', '082349122292', 'Pegawai', 'BRAVO'],
            ['MOHAMAD ANDI MAHARDIKA', '199603122015031001', 'mohamad.mahardika@basarnas.go.id', '081243756142', 'Pegawai', 'ALFA'],
            ['HARIYANTO', '199108112015031003', 'hariyanto@basarnas.go.id', '081294124235', 'Pegawai', 'ECHO'],
            ['OGI TRI KURNIAWAN', '199509282015031001', 'ogi.kurniawan@basarnas.go.id', '085298082211', 'Pegawai', 'BRAVO'],
            ['TAHRIZAL A. RAMADANI', '199601212017121003', 'tahrizal.ramadani@basarnas.go.id', '082292714609', 'Pegawai', 'DELTA'],
            ['IRVAN RAHARJAN', '199704042017121007', 'irvan.raharjan@basarnas.go.id', '082259060936', 'Pegawai', 'BRAVO'],
            ['MOH.RIVAI', '199701162017121003', 'moh.rivai@basarnas.go.id', '082271348332', 'Pegawai', 'BRAVO'],
            ['ALI FAJAR ZODIK', '199706062017121006', 'ali.zodik@basarnas.go.id', '085256500364', 'Pegawai', 'ALFA'],
            ['MANDASARI HANINGTYAS', '199505012020122004', 'mandasari.haningtyas@basarnas.go.id', '089638888558', 'Pegawai', 'DELTA'],
            ['MOH. AGUS BUDIMAN', '199708162020121001', 'moh.budiman@basarnas.go.id', '082259833887', 'Pegawai', 'CHARLIE'],
            ['ARASPATI PUTRA PERWIRA UTAMA', '200205102025061001', 'araspati.utama@basarnas.go.id', '082191261787', 'Pegawai', 'ALFA'],
            ['IMRAN AMINULLAH', '200109222025061003', 'imran.aminullah@basarnas.go.id', '087837852799', 'Pegawai', 'DELTA'],
            ['SITI NURHANISA', '200605212025062001', 'siti.nurhanisa@basarnas.go.id', '083878573179', 'Pegawai', 'ECHO'],
            ['FAJARUDDIN', '200004142025061005', 'fajaruddin.fajaruddin@basarnas.go.id', '082266871815', 'Pegawai', 'ALFA'],
            ['KURNIA', '200403142025062001', 'kurnia@basarnas.go.id', '082296542131', 'Pegawai', 'CHARLIE'],
            ['MUH. FAJAR ARFAH', '200401312025061001', 'muh.arfah@basarnas.go.id', '085341807331', 'Pegawai', 'BRAVO'],
            ['CARSTEN GLEEN HASAN', '200201242025061004', 'carsten.hasan@basarnas.go.id', '085238546335', 'Pegawai', 'ECHO'],
            ['MARDIN', '200209052025061003', 'mardin@basarnas.go.id', '082292052549', 'Pegawai', 'ALFA'],
            ['ALDI SONO', '200209212025061001', 'aldi.sono@basarnas.go.id', '083826166247', 'Pegawai', 'DELTA'],
            ['ERLANGGA SATRIA PUTRA WARDANA', '200502162025061002', 'erlangga.wardana@basarnas.go.id', '087817594635', 'Pegawai', 'BRAVO'],
            ['ANDI MAGFIRATUL MURADIFAH', '200105092025062003', 'andi.muradifah@basarnas.go.id', '0895601846472', 'Pegawai', 'BRAVO'],
            ['HAIKAL ANANDA PRATAMA', '200210012025061001', 'haikal.pratama@basarnas.go.id', '083863222189', 'Pegawai', 'CHARLIE'],
            ['MUHAMMAD ARFIAN PRATAMA', '200208212025061002', 'arfian.pratama@basarnas.go.id', '082142560229', 'Pegawai', 'DELTA'],
            ['LA ODE KAHAR DAFIQ', '200404182025061001', 'la.dafiq@basarnas.go.id', '081341912635', 'Pegawai', 'BRAVO'],
            ['FIRGIANSYAH', '200207212025061002', 'firgiansyah.firgiansyah@basarnas.go.id', '085117404797', 'Pegawai', 'ECHO'],
            ['FAHRIL IRFAN', '200110142025061001', 'fahril.irfan@basarnas.go.id', '081236713432', 'Pegawai', 'CHARLIE'],
            ['YUSUF PUTRA PRADANA', '200104302025061005', 'yusuf.pradana@basarnas.go.id', '082297274619', 'Pegawai', 'CHARLIE'],
            ['ARIO ADI SATRIA WIBOWO', '200302272025061002', 'ario.wibowo@basarnas.go.id', '0895605132667', 'Pegawai', 'ECHO'],
            ['DJOKO IRAWAN', '197606112025211028', 'djoko.irawan@basarnas.go.id', '081317772622', 'Pegawai', 'DELTA'],
            ['HABRULLAH', '198506282025211044', 'habrullah@basarnas.go.id', '085299461558', 'Pegawai', 'ECHO'],
        ];

        foreach ($users as $u) {
            User::create([
                'name' => $u[0],
                'nip' => $u[1],
                'email' => $u[2],
                'phone' => $u[3],
                'role' => $u[4],
                'team_id' => $u[5] ? $teamIds[$u[5]] : null,
                'institution' => null,
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
            ]);
        }

        /* ===========================
         *  Insert Vehicles
         * =========================== */
        $vehicles = [
            ['Rescue Car Double cabin 01', 'DN 8870 A', 3020101003],
            ['Rescue Car Double cabin 02', 'B 9425 POR', 3020101004],
            ['Rescue Car Double cabin Hilux', 'B 9228 PSE', 3020105129],
            ['Rescue Car Carrie Commob', 'B 1577 PQR', 3020105060],
            ['Rescue Car Carrie Ambulance', 'B 1072 PQR', 3020105061],
            ['Truck Personil 03', 'B 9599 PQR', 3020105062],
            ['Truck Personil 06', 'B 9986 POQ', 3020105063],
            ['Rescue Truck', 'B 9091 PQR', 3020105064],
            ['Truck Pengangkut ATV', 'B 9033 POR', 3020101005],
        ];

        foreach ($vehicles as $v) {
            Vehicle::create([
                'name' => $v[0],
                'plat_nomor' => $v[1],
                'kode_bmn' => $v[2],
                'status' => 'available',
            ]);
        }
    }
}
