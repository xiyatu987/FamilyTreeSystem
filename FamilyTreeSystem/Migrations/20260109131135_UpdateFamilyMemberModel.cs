using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace FamilyTreeSystem.Migrations
{
    /// <inheritdoc />
    public partial class UpdateFamilyMemberModel : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_FamilyMembers_FamilyMembers_FatherId",
                table: "FamilyMembers");

            migrationBuilder.DropForeignKey(
                name: "FK_FamilyMembers_FamilyMembers_MotherId",
                table: "FamilyMembers");

            migrationBuilder.DropIndex(
                name: "IX_FamilyMembers_FatherId",
                table: "FamilyMembers");

            migrationBuilder.DropIndex(
                name: "IX_FamilyMembers_MotherId",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "BirthDate",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "BirthPlace",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "DeathDate",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "DeathPlace",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "FatherId",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "FatherRelationType",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "LastName",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "MotherId",
                table: "FamilyMembers");

            migrationBuilder.DropColumn(
                name: "MotherRelationType",
                table: "FamilyMembers");

            migrationBuilder.RenameColumn(
                name: "FirstName",
                table: "FamilyMembers",
                newName: "FullName");
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.RenameColumn(
                name: "FullName",
                table: "FamilyMembers",
                newName: "FirstName");

            migrationBuilder.AddColumn<DateTime>(
                name: "BirthDate",
                table: "FamilyMembers",
                type: "TEXT",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "BirthPlace",
                table: "FamilyMembers",
                type: "TEXT",
                maxLength: 255,
                nullable: true);

            migrationBuilder.AddColumn<DateTime>(
                name: "DeathDate",
                table: "FamilyMembers",
                type: "TEXT",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "DeathPlace",
                table: "FamilyMembers",
                type: "TEXT",
                maxLength: 255,
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "FatherId",
                table: "FamilyMembers",
                type: "INTEGER",
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "FatherRelationType",
                table: "FamilyMembers",
                type: "INTEGER",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "LastName",
                table: "FamilyMembers",
                type: "TEXT",
                maxLength: 5,
                nullable: false,
                defaultValue: "");

            migrationBuilder.AddColumn<int>(
                name: "MotherId",
                table: "FamilyMembers",
                type: "INTEGER",
                nullable: true);

            migrationBuilder.AddColumn<int>(
                name: "MotherRelationType",
                table: "FamilyMembers",
                type: "INTEGER",
                nullable: true);

            migrationBuilder.CreateIndex(
                name: "IX_FamilyMembers_FatherId",
                table: "FamilyMembers",
                column: "FatherId");

            migrationBuilder.CreateIndex(
                name: "IX_FamilyMembers_MotherId",
                table: "FamilyMembers",
                column: "MotherId");

            migrationBuilder.AddForeignKey(
                name: "FK_FamilyMembers_FamilyMembers_FatherId",
                table: "FamilyMembers",
                column: "FatherId",
                principalTable: "FamilyMembers",
                principalColumn: "Id",
                onDelete: ReferentialAction.SetNull);

            migrationBuilder.AddForeignKey(
                name: "FK_FamilyMembers_FamilyMembers_MotherId",
                table: "FamilyMembers",
                column: "MotherId",
                principalTable: "FamilyMembers",
                principalColumn: "Id",
                onDelete: ReferentialAction.SetNull);
        }
    }
}
