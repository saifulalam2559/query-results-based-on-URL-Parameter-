<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kunden;
use Illuminate\Support\Str;
use Auth;
use App\Models\Note;
use Carbon\Carbon;
use Excel;
use App\Imports\KundenImport;
use App\Imports\NoteImport;

use Illuminate\Support\Facades\DB;

class KundenController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function index()
    {
    }

    /*****************************************
   Query Filtering in the listing page
  ****************************************/

    public function aukundenListing(Request $request)
    {
        $ukunden5 = Kunden::query();

        $sort = "";

        if ($request->sortBy != null) {
            $sort = $request->sortBy;
        }

        if (!empty($_GET["sortBy"])) {
            $sort = $_GET["sortBy"];

            if ($sort == "ja-online") {
                $ukundens = $ukunden5->where("status", "active")->paginate(50);
            }

            if ($sort == "nicht-online") {
                $ukundens = $ukunden5
                    ->where("status", "inactive")
                    ->paginate(50);
            }

            if ($sort == "alle-password") {
                $password = $request->password;
                $ukundens = $ukunden5
                    ->where("password", "like", "%" . $password . "%")
                    ->where("account_created", "active")
                    ->paginate(50);
            }

            if ($sort == "active-password-nicht-online") {
                $password = $request->password;
                $ukundens = $ukunden5
                    ->where("password", "like", "%" . $password . "%")
                    ->where("account_created", "active")
                    ->where("status", "inactive")
                    ->paginate(50);
            }

            if ($sort == "no-password") {
                $ukundens = $ukunden5
                    ->where("password", "=", null)
                    ->paginate(50);
            }

            if ($sort == "ja-bezahlt") {
                $ukundens = $ukunden5->where("bezahlt", "active")->paginate(50);
            }

            if ($sort == "nicht-bezahlt") {
                $ukundens = $ukunden5
                    ->where("bezahlt", "inactive")
                    ->paginate(50);
            }

            if ($sort == "email-gesendet") {
                $ukundens = $ukunden5
                    ->where("email_sent", "active")
                    ->paginate(50);
            }

            if ($sort == "email-nicht-gesendet") {
                $ukundens = $ukunden5
                    ->where("email_sent", "inactive")
                    ->paginate(50);
            }

            if ($sort == "angerufen") {
                $ukundens = $ukunden5
                    ->where("kundelust", "angerufen")
                    ->paginate(50);
            }

            if ($sort == "kunde") {
                $ukundens = $ukunden5
                    ->where("kundelust", "kunde")
                    ->paginate(50);
            }

            if ($sort == "keinkunde") {
                $ukundens = $ukunden5
                    ->where("kundelust", "keinkunde")
                    ->paginate(50);
            }

            if ($sort == "kundeselbsterstellt") {
                $ukundens = $ukunden5
                    ->where("kundelust", "kundeselbsterstellt")
                    ->paginate(50);
            }

            if ($sort == "interesse") {
                $ukundens = $ukunden5
                    ->where("kundelust", "interesse")
                    ->paginate(50);
            }

            if ($sort == "keininteresse") {
                $ukundens = $ukunden5
                    ->where("kundelust", "keininteresse")
                    ->paginate(50);
            }

            if ($sort == "interessespater") {
                $ukundens = $ukunden5
                    ->where("kundelust", "interessespater")
                    ->paginate(50);
            }

            if ($sort == "kundezugangsdaten") {
                $ukundens = $ukunden5
                    ->where("kundelust", "kundezugangsdaten")
                    ->paginate(50);
            }

            // end kunde kunde kunde

            if ($sort == "AWINDA") {
                $ukundens = $ukunden5
                    ->whereJsonContains("interesse", "AWINDA")
                    ->paginate(50);
            }

            if ($sort == "JOB_PORTAL") {
                $ukundens = $ukunden5
                    ->whereJsonContains("interesse", "JOB PORTAL")
                    ->paginate(50);
            }

            if ($sort == "COMPANIE_PORTAL") {
                $ukundens = $ukunden5
                    ->whereJsonContains("interesse", "COMPANIE PORTAL")
                    ->paginate(50);
            }

            if ($sort == "Österreich") {
                $ukundens = $ukunden5
                    ->where("auslandosterreich", "Österreich")
                    ->paginate(50);
            }

            if ($sort == "Schweiz") {
                $ukundens = $ukunden5
                    ->where("auslandschweiz", "Schweiz")
                    ->paginate(50);
            }

            if ($sort == "Deutschland") {
                $ukundens = $ukunden5
                    ->where("deutschland", "Deutschland")
                    ->paginate(50);
            }

            if ($sort == "weiterelander") {
                $ukundens = $ukunden5
                    ->where("weiterelander", "weitere Länder")
                    ->paginate(50);
            }

            if ($sort == "niederlande") {
                $ukundens = $ukunden5
                    ->where("niederlande", "Niederlande")
                    ->paginate(50);
            }

            if ($sort == "luxemburg") {
                $ukundens = $ukunden5
                    ->where("luxemburg", "Luxemburg")
                    ->paginate(50);
            }

            if ($sort == "frankreich") {
                $ukundens = $ukunden5
                    ->where("frankreich", "Frankreich")
                    ->paginate(50);
            }

            if ($sort == "belgien") {
                $ukundens = $ukunden5
                    ->where("belgien", "Belgien")
                    ->paginate(50);
            }

            if ($sort == "danemark") {
                $ukundens = $ukunden5
                    ->where("danemark", "Dänemark")
                    ->paginate(50);
            }

            if ($sort == "polen") {
                $ukundens = $ukunden5->where("polen", "Polen")->paginate(50);
            }

            if ($sort == "tschechei") {
                $ukundens = $ukunden5
                    ->where("tschechei", "Tschechei")
                    ->paginate(50);
            }
        }

        $query = $request->input("query");

        $squery = "";

        if ($request->query != null) {
            $squery = $request->query;
        }

        if (!empty($_GET["query"])) {
            $query = $request->input("query");

            $ukundens = $ukunden5
                ->where("ansprechpartner_email", "LIKE", "%" . $query . "%")
                ->paginate(50);
        }

        $firmennamequery = $request->input("firmenname");

        $squery = "";

        if ($request->firmenname != null) {
            $squery = $request->firmenname;
        }

        if (!empty($_GET["firmenname"])) {
            $firmennamequery = $request->input("firmenname");

            $ukundens = $ukunden5
                ->where("firmenname", "LIKE", "%" . $firmennamequery . "%")
                ->paginate(50);
        }

        $current = Carbon::today("Europe/Berlin");
        $yesterday = Carbon::yesterday("Europe/Berlin");
        $todaysposts = Kunden::whereDate("created_at", "=", $current)->get();
        $yesterdayposts = Kunden::whereDate(
            "created_at",
            "=",
            $yesterday
        )->get();

        $saifultodayentryyesterday = Kunden::whereDate(
            "updated_at",
            "=",
            $yesterday
        )
            ->where("status", "=", "active")
            ->get();

        $saifultodayentry = Kunden::whereDate("updated_at", "=", $current)
            ->where("status", "=", "active")
            ->orderBy("updated_at", "desc")
            ->get();

        $freelancer1todaysposts = Kunden::whereDate("updated_at", "=", $current)
            ->where("status", "=", "active")
            ->where("freelancer", "freelancer1")
            ->orderBy("updated_at", "desc")
            ->get();
        $freelancer1yesterdayposts = Kunden::whereDate(
            "updated_at",
            "=",
            $yesterday
        )
            ->where("status", "=", "active")
            ->where("freelancer", "freelancer1")
            ->get();

        $ukundens = $ukunden5->orderBy("id", "DESC")->paginate(50);

        return view(
            "backend.admin.kunden.index",
            compact(
                "ukundens",
                "sort",
                "query",
                "firmennamequery",
                "todaysposts",
                "current",
                "yesterday",
                "yesterdayposts",
                "freelancer1todaysposts",
                "freelancer1yesterdayposts",
                "saifultodayentry",
                "saifultodayentryyesterday"
            )
        );
    }

    /*****************************************
   the query results based on URL-Parameter 
  ****************************************/

    public function aukundenFilter(Request $request)
    {
        $data = $request->all();

        $sortByUrl = "";

        if (!empty($data["sortBy"])) {
            $sortByUrl .= "&sortBy=" . $data["sortBy"];
        }

        $queryByUrl = "";

        if (!empty($data["query"])) {
            $queryByUrl .= "&query=" . $data["query"];
        }

        $firmaqueryByUrl = "";

        if (!empty($data["firmenname"])) {
            $firmaqueryByUrl .= "&firmenname=" . $data["firmenname"];
        }

        return redirect()->route(
            "aukunden.listing",
            $sortByUrl . $queryByUrl . $firmaqueryByUrl
        );
    }

    public function aukundenStatus1($id)
    {
        $getStatus = Kunden::select("status")
            ->where("id", $id)
            ->first();

        if ($getStatus->status == "active") {
            $status = "inactive";
        } else {
            $status = "active";
        }

        Kunden::where("id", $id)->update(["status" => $status]);
        return redirect()
            ->route("aukunden.listing")
            ->with("success", "Status wurde geändert!");
    }

    public function aukundenbezahltStatus($id)
    {
        $getStatus = Kunden::select("bezahlt")
            ->where("id", $id)
            ->first();

        if ($getStatus->bezahlt == "active") {
            $status = "inactive";
        } else {
            $status = "active";
        }

        Kunden::where("id", $id)->update(["bezahlt" => $status]);
        return redirect()
            ->route("aukunden.listing")
            ->with("success", "Zahlungsstatus wurde geändert!");
    }

    public function aukundenemailgesendetStatus($id)
    {
        $getStatus = Kunden::select("email_sent")
            ->where("id", $id)
            ->first();

        if ($getStatus->email_sent == "active") {
            $status = "inactive";
        } else {
            $status = "active";
        }

        Kunden::where("id", $id)->update(["email_sent" => $status]);
        return redirect()
            ->route("aukunden.listing")
            ->with("success", "E-Mail gesendet wurde geändert!");
    }

    public function create()
    {
        $ukundens = Kunden::orderBy("id", "DESC")->get();
        return view("backend.admin.kunden.create", compact("ukundens"));
    }

    /*****************************************
   Store data into DB table
  ****************************************/

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                "firmenname" => "required",
                "domain" => "required|url",
                "ansprechpartner_email" => "required|unique:kundens",
                "category_name" => "nullable",
                "category_link" => "nullable",
                "password" => "nullable",
                "fake_email" => "nullable|unique:kundens",
                "interesse" => "nullable",
                "plz" => "nullable",
                "ort" => "nullable",
            ],

            [
                "firmenname.required" =>
                    "Name Unternehmen ist ein Pflichtfeld.",
                "domain.required" => "Domain ist ein Pflichtfeld.",
                "domain.url" => "Domain funktioniert nicht ohne https://",
                "ansprechpartner_email.required" =>
                    "E-Mail ist ein Pflichtfeld.",
                "category_name.required" =>
                    "Branche / Kategorie: Location / Anbieter ist ein Pflichtfeld.",
                "category_link.required" =>
                    "Lamangoo.de / Location / Anbieter ist ein Pflichtfeld.",
                "ansprechpartner_email.unique" =>
                    "Dies ist ein doppelter Eintrag. Wir haben einen Eintrag mit dieser E-Mail.",
                "fake_email.unique" =>
                    "Dies ist ein doppelter Eintrag. Wir haben einen Eintrag mit dieser E-Mail.",
                "fake_email.required" =>
                    "Fake E-Mail Login Account ist ein Pflichtfeld.",
            ]
        );

        $data["user_id"] = Auth::user()->id;
        $data["firmenname"] = $request->firmenname;
        $data["domain"] = $request->domain;
        $data["ansprechpartner_email"] = $request->ansprechpartner_email;
        $data["ansprechpartner_name"] = $request->ansprechpartner_name;
        $data["category_link"] = $request->category_link;
        $data["status"] = $request->status;
        $data["start_date"] = $request->start_date;
        $data["end_date"] = $request->end_date;
        $data["inhaber"] = $request->inhaber;
        $data["inhaber_tel"] = $request->inhaber_tel;
        $data["ansprechpartner_tel"] = $request->ansprechpartner_tel;
        $data["email_sent"] = $request->email_sent;
        $data["againansprechpartner_email"] =
        $request->againansprechpartner_email;
        $data["kundelust"] = $request->kundelust;
        $data["angerufen_date"] = $request->angerufen_date;
        $data["anruftermin"] = $request->anruftermin;
        $data["small_note"] = $request->small_note;
        $data["lamangoolive_link"] = $request->lamangoolive_link;
        $data["interesse"] = json_encode($request->interesse);
        $data["auslandosterreich"] = $request->auslandosterreich;
        $data["auslandschweiz"] = $request->auslandschweiz;
        $data["deutschland"] = $request->deutschland;
        $data["weiterelander"] = $request->weiterelander;
        $data["niederlande"] = $request->niederlande;
        $data["luxemburg"] = $request->luxemburg;
        $data["frankreich"] = $request->frankreich;
        $data["belgien"] = $request->belgien;
        $data["danemark"] = $request->danemark;
        $data["polen"] = $request->polen;
        $data["tschechei"] = $request->tschechei;
        $data["bundesland"] = $request->bundesland;
        $data["plz"] = $request->plz;
        $data["ort"] = $request->ort;
        $data["account_created"] = $request->account_created;
        $data["freelancer"] = $request->freelancer;
        $data["password_changed"] = $request->password_changed;

        $fakeemail = $request->input("ansprechpartner_email");
        $text = "neu.";
        $data["fake_email"] = $text . $fakeemail;

        $sets = [];
        $sets[] = "^&";
        $sets[] = '$%';
        $sets[] = "@#";
        $sets[] = "*{";
        $sets[] = "?/";
        $sets[] = ":_";
        $sets[] = '!$';
        $sets[] = "*&";
        $sets[] = "?&";
        $sets[] = "*/";
        $sets[] = "&@";

        $randomSet = $sets[array_rand($sets)]; //get a random set

        $data["password"] = Str::random(3) . $randomSet . Str::random(5);

        if (
            $request->input("category_link") ==
            "https://www.abc.de/anbieter/brautkleider"
        ) {
            $data["category_name"] = "Brautkleider";
        } elseif (
            $request->input("category_link") ==
            "https://www.abc.de/anbieter/schmuck"
        ) {
            $data["category_name"] = "Schmuck";
        } else {
            $data["category_name"] = " ";
        }

        $slug = Str::slug($request->input("firmenname"));
        $slug_count = Kunden::where("slug", $slug)->count();

        if ($slug_count > 0) {
            $slug = time() . "-" . $slug;
        }

        $data["slug"] = $slug;

        $status = Kunden::create($data);

        if ($status) {
            return redirect()
                ->route("aukunden.listing")
                ->with("success", "Kunden wurde erfolgreich erstellt !");
        } else {
            return redirect()
                ->back()
                ->with("error", "Stimmt etwas nicht");
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $ukunden = Kunden::find($id);

        if ($ukunden) {
            return view("backend.admin.kunden.edit", compact("ukunden"));
        } else {
            return back()->with("error", "Daten nicht gefunden!!");
        }
    }

    public function update(Request $request, $id)
    {
        $ukunden = Kunden::find($id);

        if ($ukunden) {
            $this->validate($request, [
                "firmenname" => "string|required",
                "domain" => "string|required",
                "ansprechpartner_email" => "string|required",

                "password" => "nullable",
                "lamangoolive_link" => "nullable",
                "interesse" => "nullable",
            ]);

            $data = $request->all();

            $ukunden->fill($data)->save();

            return redirect()
                ->back()
                ->with("success", "Kunde wurde erfolgreich erstellt !");
        } else {
            return redirect()
                ->route("admin.dashboard")
                ->with("error", "Etwas ist schief gelaufen");
        }
    }

    public function destroy($id)
    {
    }

    public function aukundenSlug($slug)
    {
        $ukunden = Kunden::where("slug", $slug)->first();

        if ($ukunden) {
            return view("backend.admin.kunden.preview", compact("ukunden"));
        } else {
            return redirect()->route("/");
        }
    }

    /*****************************************
    Autocomplete Search
  ****************************************/

    public function auautoSearchkundenEmail(Request $request)
    {
        $query = $request->get("term", "");

        $ukundens = Kunden::where(
            "ansprechpartner_email",
            "LIKE",
            "%" . $query . "%"
        )->get();

        $data = [];
        foreach ($ukundens as $kunden) {
            $data[] = [
                "value" => [$kunden->ansprechpartner_email],
                "id" => $kunden->id,
            ];
        }

        if (count($data)) {
            return $data;
        } else {
            return ["value" => "keine email gefunden", "id" => ""];
        }
    }

    public function adminuautoSearchfirmenname(Request $request)
    {
        $query = $request->get("term", "");

        $ukundens = Kunden::where(
            "firmenname",
            "LIKE",
            "%" . $query . "%"
        )->get();

        $data = [];
        foreach ($ukundens as $kunden) {
            $data[] = ["value" => [$kunden->firmenname], "id" => $kunden->id];
        }

        if (count($data)) {
            return $data;
        } else {
            return ["value" => "keine Unternehmen gefunden", "id" => ""];
        }
    }

    /*****************************************
    Add Note based on user id 
  ****************************************/

    public function auaddNote(Request $request, $id = null)
    {
        $kunden = Kunden::where(["id" => $id])->first();

        if ($request->isMethod("post")) {
            $request->validate([
                "note_title" => "required",
                "note_description" => "required",
            ]);

            $data = new Note();
            $data->user_id = Auth::user()->id;
            $data->note_title = $request->note_title;
            $data->note_description = $request->note_description;
            $data->kunden_id = $request->kunden_id;

            $data->save();

            return redirect()
                ->back()
                ->with("success", "Hinweis wurde hinzugefügt!!!");
        }

        $notes = Note::where("kunden_id", $id)->get();

        return view("backend.admin.kunden.note", compact("kunden", "notes"));
    }

    public function aukundenDelete($id)
    {
        $kundendelete = Kunden::where(["id" => $id])->first();

        $kundendelete->where(["id" => $id])->delete();

        return redirect()
            ->back()
            ->with("success", "Kundendaten wurden gelöscht!!");
    }

    public function audeleteNote($id)
    {
        $notedelete = Note::find($id);

        if ($notedelete) {
            $status = $notedelete->delete();

            if ($status) {
                return redirect()
                    ->back()
                    ->with("success", "Note wurde gelöscht!!");
            } else {
                return back()->with("error", "Etwas ist schief gelaufen!!");
            }
        } else {
            return back()->with("error", "Daten nicht gefunden!!");
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate(
            [
                "file" => "required|mimes:xls,xlsx",
            ],

            [
                "file.required" => "Name Unternehmen ist ein Pflichtfeld.",
            ]
        );

        Excel::import(new KundenImport(), $request->file);
        Excel::import(new NoteImport(), $request->file);

        return redirect()
            ->route("aukunden.listing")
            ->with("success", "Daten aktualisiert");
    }

    public function detailEintrag()
    {
        $one_year_ago = Carbon::now()
            ->subDays(365)
            ->format("Y-m-d");

        $dates = Kunden::where("updated_at", ">=", $one_year_ago)
            ->where("status", "=", "active")
            ->groupBy("date")
            ->orderBy("date", "DESC")
            ->get([
                DB::raw("Date(updated_at) as date"),
                DB::raw('COUNT(*) as "count"'),
            ]);

        return view("backend.admin.kunden.detaileintrag", compact("dates"));
    }
}
