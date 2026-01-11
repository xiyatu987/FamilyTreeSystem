using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace FamilyTreeSystem.Migrations
{
    /// <inheritdoc />
    public partial class AddPersonalInfoFields : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "Name",
                table: "FamilyMembers",
                newName: "FirstName");

            migrationBuilder.AddColumn<string>(
                name: "CertificateAddress",
                table: "FamilyMembers",
                type: "TEXT",
                maxLength: 255,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "EducationLevel",
                table: "FamilyMembers",
                type: "TEXT",
                maxLength: 50,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "FormerName",
                table: "FamilyMembers",
                type: "TEXT",
                maxLength: 20,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "GraduateSchool",
                table: "FamilyMembers",
                type: "TEXT",
                maxLength: 100,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "IDCardNumber",
                table: "FamilyMembers",
                type: "TEXT",
                maxLength: 18,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "LastName",
                table: "FamilyMembers",
                type: "TEXT",
                maxLength: 5,
                nullable: false,
                defaultValue: "");

            migrationBuilder.AddColumn<decimal>(
                name: "MeritContribution",
                table: "FamilyMembers",
                type: "decimal(18,2)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "PhoneNumber",
                table: "FamilyMembers",
                type: "TEXT",
                maxLength: 20,
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "WorkUnit",
                table: "FamilyMembers",
                type: "TEXT",
                maxLength: 100,
                nullable: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "CertificateAddress",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "EducationLevel",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "FormerName",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "GraduateSchool",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "IDCardNumber",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "LastName",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "MeritContribution",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "PhoneNumber",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "WorkUnit",
                table: "FamilyMembers");

            migrationBuilder.RenameColumn(
                name: "FirstName",
                table: "FamilyMembers",
                newName: "Name");
        }
    }
}
