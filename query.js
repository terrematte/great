function checked(name) {
    var a = [];

    $("input[name='" + name + "']:checked").each(function() {
        a.push($(this).val());
    });
    return a;
}

function read_num(name) {
    return parseInt($("input[name='" + name + "']").val());
}

function read_atoms(name) {
    var a = $("input[name='" + name + "']").val().split(/ *, */);
    return $.grep(a, function (n) {  return n; });
}

function form_value() {
    return {
	code: $("input[name='code']").val(),
        num_exercises: read_num('num_exercises') * num_students,
        num_students: num_students,
        atoms: read_atoms('atoms'),
        compl_min: read_num('compl_min'),
        compl_max: read_num('compl_max'),
        num_premises: read_num('num_premises'),
        conectives: checked('conectives'),
        restrictions: checked('restrictions')
    };
}

function proofweb(e) {
    return e.replace(/&/g, "/\\").replace(/\|/g, "\\/").replace(/!/g, "~");
}

function natural_ded(atoms, ex, n) {

    var m = ["Require Import ProofWeb.",
	     "(* DEMONSTRAR EM DEDUÇÃO NATURAL: *)",
	     "Parameter "].join('\n\n');

    m += atoms.join(' ');

    m += ": Prop.\n\n";

    var k = 0;

    for(i in ex.premises) {
	m += "Hypothesis P" + k + " : ";
	m += proofweb(ex.premises[i]) + ".\n\n";
	k = k + 1;
    }

    m += "Theorem T" + n + " : " + proofweb(ex.conclusion) + ".\n\n";

    m += ["Proof.",
	  "(* Prova aqui *)",
	  "Qed.\n"].join("\n\n");

    return m;
}

function semantic(atoms, ex, n) {

    var m = ["Require Import Semantics.",
	    "(* REFUTAR EM TEORIA SEMÂNTICA: *)",
	     "Parameter "].join('\n\n');

    m += atoms.join(' ');

    m += ": Prop.\n\n";

    var k = 0;

    for(i in atoms) {
	m += "(* Hypothesis P" + k + " : (v ?? ";
	m += atoms[i] + "). *)\n\n";
	k = k + 1;
    }

    console.log(ex.premises);

    var r = ex.premises.map(function (d) { return proofweb(d); });

    console.log(r);

    console.log('risos');
    console.log(r.join(' /\\ '));
    console.log(ex.conclusion);
    console.log(proofweb(ex.conclusion));
    

    var p = ' ( ( ' + r.join(' /\\ ') + ' ) -> ' + proofweb(ex.conclusion) + ' ) ';

    console.log(p);

    m += "Theorem T" + n + " : (v ||-/- " + p + ").\n\n";

    m += ["Proof.",
	  "(* Prova aqui *)",
	  "Qed.\n"].join("\n\n");

    return m;
}

function shuffle(array) {
    var tmp, current, top = array.length;

    if(top) while(--top) {
        current = Math.floor(Math.random() * (top + 1));
        tmp = array[current];
        array[current] = array[top];
        array[top] = tmp;
    }

    return array;
}

function combine(res) {
    var a = shuffle(res.exercises.valid.concat(res.exercises.invalid));

    console.log(a);

    var r = {};

    $.each(students, function(i, val) {
	r[val] = { nd: [], sem: [] };

	var l = r[val];

	var j;

	var rte = res.request.num_exercises / num_students;

	for (j = 0; j < rte; j++) {
	    console.log(i + " " + j);
	    l.nd[j] = natural_ded(res.request.atoms, a[i * rte + j], j);
	    l.sem[j] = semantic(res.request.atoms, a[i * rte + j], j);
	}
    });

    return { code: res.request.code, data: r };
};


//console.log(natural_ded(s.request.atoms, s.exercises.valid[0], 0));
//console.log(semantic(s.request.atoms, s.exercises.valid[0], 0));

function select(nome) {
    $('#side .tab').hide();
    $('#side .tab#c_' + nome).show();
    $('#menu .selected').removeClass('selected');
    $('#menu #' + nome).addClass('selected');
}

var test;

$(document).ready(function () {

    $("a#salvar").click(function (ev) {
        $.ajax({
            url:"write.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(combine(test)),
            error: function(obj, status) {
                alert("Erro ao salvar: " + status);
            },
            success: function(data) {
		console.log('vejamos?');
            }
        });
    });

    $("a#send").click(function (ev) {
        $('a#send').toggle();
	$('a#salvar').hide();

        $.ajax({
            url:"generate.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(form_value()),
            error: function(obj, status) {
                alert("Erro ao enviar os dados: " + status);
            },
            success: function(data) {
                $('#c_detalhes').show().html('<pre>' + JSON.stringify(data, null, 4));
		$('#c_exerc').show().html('<pre>' + JSON.stringify(combine(data), null, 4));

		test = data;

                $('a#send').toggle();
                $('#menu').show();
                $('a#salvar').show();
		
                $('#menu #exerc').trigger('click');
            }
        });
    });

    $('form input').bind('change blur', function () {
        $('textarea[name="codigo"]').val(JSON.stringify(form_value(), null, 4));
    });

    $('#side a').bind('click', function(ev) {
        select(this.id);
    });

    $('input[name=num_exercises]')
        .bind('blur', function (ev) {
            var v = $(this).val() || 0;
            $(this).val(v);
            $('span#num_total').text($(this).val() * num_students);
        })
        .val(def.num_exercises)
        .trigger('blur');


});
